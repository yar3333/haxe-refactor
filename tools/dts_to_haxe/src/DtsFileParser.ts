/// <reference path="../typings/globals/node/index.d.ts" />

import * as ts from "typescript";
import { StringTools } from "./StringTools"
import { Tokens } from "./Tokens";
import { HaxeTypeDeclaration, HaxeVar } from "./HaxeTypeDeclaration";
import { ILogger } from "./ILogger";
import { TypeMapper } from "./TypeMapper";
import { TypeConvertor } from "./TypeConvertor";
import { TypePathTools } from "./TypePathTools";

export class DtsFileParser
{
    private logger : ILogger;

    private tokens : string[];
    private indent = "";

    private typeConvertor : TypeConvertor;

    private imports = new Array<string>();
    public allHaxeTypes: Array<HaxeTypeDeclaration>;

    public curPackage: string;

    constructor(private sourceFile: ts.SourceFile, private typeChecker: ts.TypeChecker, typeMapper:TypeMapper, private rootPackage:string, private nativeNamespace:string, private typedefs:Array<string>, private knownTypes:Array<string>)
    {
        this.tokens = Tokens.getAll();
        this.typeConvertor = new TypeConvertor(this, typeMapper, knownTypes);
    }

    parse(allHaxeTypes:Array<HaxeTypeDeclaration>, logger:ILogger) : void
    {
        this.allHaxeTypes = allHaxeTypes;
        this.logger = logger;
        this.curPackage = this.rootPackage;

        const node = this.sourceFile;

        var savePack = this.curPackage;

        ts.forEachChild(node, x => {
            if (x.kind == ts.SyntaxKind.NamespaceExportDeclaration) {
                this.curPackage = TypePathTools.makeFullClassPath([ this.curPackage, (<ts.NamespaceExportDeclaration>x).name.getText() ]);
            }
        });

        this.processNode(node, () => {
            switch (node.kind) {
                case ts.SyntaxKind.SourceFile:
                    this.processChildren(node, new Map<number, (node:any) => void>(
                    [
                        [ ts.SyntaxKind.ImportDeclaration, (x:ts.ImportDeclaration) => this.processImportDeclaration(x) ],
                        [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
                        [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
                        [ ts.SyntaxKind.VariableStatement, (x:ts.VariableStatement) => this.processVariableStatement(x) ],
                        [ ts.SyntaxKind.EnumDeclaration, (x:ts.EnumDeclaration) => this.processEnumDeclaration(x) ],
                        [ ts.SyntaxKind.FunctionDeclaration, (x:ts.FunctionDeclaration) => this.processFunctionDeclaration(x) ],
                        [ ts.SyntaxKind.ModuleDeclaration, (x:ts.ModuleDeclaration) => this.processModuleDeclaration(x) ],
                        [ ts.SyntaxKind.NamespaceExportDeclaration, (x:ts.NamespaceExportDeclaration) => {} ],
                        [ ts.SyntaxKind.EndOfFileToken, (x) => {} ]
                    ]));
                    break;

                default:
                    this.logger.log(this.indent + "^----- UNKNOW ROOT ELEMENT");
                    this.logSubTree(node);
            }
        });

        this.curPackage = savePack;
    }

    parseLiteralType(node:ts.TypeLiteralNode) : string
    {
       var item = new HaxeTypeDeclaration("");
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.PropertySignature, (x:ts.PropertySignature) => this.processPropertySignature(x, item) ],
            [ ts.SyntaxKind.MethodSignature, (x:ts.MethodSignature) => this.processMethodSignature(x, item) ]
        ]));

        return item.toString();
    }

    addNewEnumAsStringAbstract(localePath:string, values:Array<string>) : HaxeTypeDeclaration
    {
        var parts = localePath.split("@");
        var varOrFunc = parts[1].split(".");
        var baseName = parts[0].toLowerCase() + "." + StringTools.capitalize(varOrFunc[varOrFunc.length - 1]);

        var name = baseName;
        var n = 0;
        while (this.allHaxeTypes.find(x => x.fullClassName.toLowerCase() == name.toLowerCase()) || this.knownTypes.find(x => x.toLowerCase() == name.toLowerCase()))
        {
            n++;
            name = baseName + "_" + n;
        }

        var item = new HaxeTypeDeclaration("abstract", name);
        item.baseFullClassName = "String";
        for (let v of values) item.addVar
        ({
            haxeName: v,
            haxeType: null,
            haxeDefVal: JSON.stringify(v),
            jsDoc: null,
            isOptional: false
        });

        this.allHaxeTypes.push(item);

        return item;
    }

    private processModuleDeclaration(node:ts.ModuleDeclaration)
    {
        var savePack = this.curPackage;
        
        this.curPackage = TypePathTools.makeFullClassPath([ this.curPackage, node.name.getText() ]);
        
        this.processChildren(node.body, new Map<number, (node:ts.Node) => void>(
        [
            [ ts.SyntaxKind.ModuleDeclaration, (x:ts.ModuleDeclaration) => this.processModuleDeclaration(x) ],
            [ ts.SyntaxKind.ModuleBlock, (x:ts.ModuleBlock) => this.processModuleBlock(x) ],
            [ ts.SyntaxKind.FunctionDeclaration, (x:ts.FunctionDeclaration) => this.processFunctionDeclaration(x) ],
            [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
            [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
            [ ts.SyntaxKind.VariableStatement, (x:ts.VariableStatement) => this.processVariableStatement(x) ],
            [ ts.SyntaxKind.EnumDeclaration, (x:ts.EnumDeclaration) => this.processEnumDeclaration(x) ],
            [ ts.SyntaxKind.TypeAliasDeclaration, (x:ts.TypeAliasDeclaration) => this.processTypeAliasDeclaration(x) ],
        ]));

        this.curPackage = savePack;
    }
    
    private processModuleBlock(node:ts.ModuleBlock)
    {
        this.processChildren(node, new Map<number, (node:ts.Node) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, x => {} ],
            [ ts.SyntaxKind.Identifier, x => {} ],
            [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
            [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
            [ ts.SyntaxKind.VariableStatement, (x:ts.VariableStatement) => this.processVariableStatement(x) ],
            [ ts.SyntaxKind.EnumDeclaration, (x:ts.EnumDeclaration) => this.processEnumDeclaration(x) ],
            [ ts.SyntaxKind.FunctionDeclaration, (x:ts.FunctionDeclaration) => this.processFunctionDeclaration(x) ],
        ]));
    }
    
    private processVariableStatement(node:ts.VariableStatement)
    {
        for (var decl of node.declarationList.declarations)
        {
            this.logger.log(this.indent + "| " + decl.name.getText())
            var isReadOnly = this.isFlag(node.declarationList, ts.NodeFlags.Const) || this.isFlag(node.declarationList, ts.NodeFlags.Readonly);
            var klass = this.getModuleClass(node);
            var varName = decl.name.getText();
            klass.addVar(
                this.createVar(varName, decl.type, null, this.getJsDoc(decl.name), false, klass.fullClassName + "@" + varName), false, true, isReadOnly
            );
        }
    }
    
    private processFunctionDeclaration(node:ts.FunctionDeclaration)
    {
        if (!this.isFlag(node, ts.NodeFlags.Export)) return;

        this.logger.log(this.indent + "| " + node.name.getText())

        var item = this.getModuleClass(node);
        var methodName = node.name.getText();

        item.addMethod
        (
            methodName,
            node.parameters.map(p => this.createVar(p.name.getText(), this.getParameterType(p), null, this.getJsDoc(p.name), node.questionToken != null, item.fullClassName + "@" + methodName+"." + p.name.getText())),
            this.typeConvertor.convert(node.type, item.fullClassName + "@" + node.name.getText()),
            null,
            false, // private
            true, // static
            this.getJsDoc(node.name),
            this.prepareTypeParameters(node)
        );
    }

    private processImportDeclaration(node:ts.ImportDeclaration)
    {
        var ids = new Array<string>();
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ImportClause, (x) => {
                this.processChildren(x, new Map<number, (node:any) => void>(
                [
                    [ ts.SyntaxKind.NamedImports, (y:ts.NamedImports) => {
                        this.processChildren(y, new Map<number, (node:any) => void>(
                        [
                            [ ts.SyntaxKind.ImportSpecifier, (z:ts.ImportSpecifier) => {
                                this.processChildren(z, new Map<number, (node:any) => void>(
                                [
                                    [ ts.SyntaxKind.Identifier, (t:ts.Identifier) => {
                                        ids.push(t.text);
                                    }]
                                ]))
                            }]
                        ]))
                    }]
                ]))
            }],

            [ ts.SyntaxKind.StringLiteral, (x:ts.StringLiteral) => {
                this.addImports(x.text, ids);
            }]
        ]));
    }

    private processInterfaceDeclaration(node:ts.InterfaceDeclaration)
    {
        var name = node.name.getText();
        
        var item : HaxeTypeDeclaration;
        switch (name)
        {
            case "Window":
                item = this.getHaxeTypeDeclarationByShort("class", this.getClassNameFromPath(node.getSourceFile().fileName), "window");
                break;

            default:
                item = this.getHaxeTypeDeclarationByShort("interface", name);
                if (this.typedefs.indexOf(item.fullClassName) >= 0) item.type = "typedef";
                break;
        }
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.TypeParameter, (x:ts.TypeParameterDeclaration) => this.processTypeParameter(x, item) ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => this.processHeritageClauseForInterface(x, item) ],
            [ ts.SyntaxKind.PropertySignature, (x:ts.PropertySignature) => this.processPropertySignature(x, item) ],
            [ ts.SyntaxKind.MethodSignature, (x:ts.MethodSignature) => this.processMethodSignature(x, item) ]
        ]));

        this.allHaxeTypes.push(item);
    }

    private processHeritageClauseForInterface(x:ts.HeritageClause, dest:HaxeTypeDeclaration)
    {
        dest.baseFullInterfaceNames = x.types.map(y => this.typeConvertor.convert(y, null));
    }

    private processClassDeclaration(node:ts.ClassDeclaration)
    {
        var item = this.getHaxeTypeDeclarationByShort("class", node.name.getText());
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.TypeParameter, (x:ts.TypeParameterDeclaration) => this.processTypeParameter(x, item) ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => this.processHeritageClauseForClass(x, item) ],
            [ ts.SyntaxKind.PropertyDeclaration, (x:ts.PropertyDeclaration) => this.processPropertyDeclaration(x, item) ],
            [ ts.SyntaxKind.MethodDeclaration, (x:ts.MethodDeclaration) => this.processMethodDeclaration(x, item) ],
            [ ts.SyntaxKind.Constructor, (x:ts.ConstructorDeclaration) => this.processConstructor(x, item) ]
        ]));

        this.allHaxeTypes.push(item);
    }
    
    private processTypeParameter(node:ts.TypeParameterDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addTypeParameter(node.name.getText(), this.typeConvertor.convert(node.constraint, dest.fullClassName + "<" + node.name.getText()));
    }

    private processEnumDeclaration(node:ts.EnumDeclaration)
    {
        var item = this.getHaxeTypeDeclarationByShort("abstract", node.name.getText());
        item.baseFullClassName = "Dynamic";
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.EnumMember, (x:ts.EnumMember) => this.processEnumMember(x, item) ],
        ]));

        this.allHaxeTypes.push(item);
    }

    private processTypeAliasDeclaration(node:ts.TypeAliasDeclaration)
    {
        var item = this.getHaxeTypeDeclarationByShort("typedef", node.name.getText());

        item.docComment = this.getJsDoc(node.name);
        if (node.typeParameters) node.typeParameters.forEach(x => this.processTypeParameter(x, item));

        item.setAliasTypeText(this.typeConvertor.convert(node.type, null));

        this.allHaxeTypes.push(item);
    }

    private processEnumMember(x:ts.EnumMember, dest:HaxeTypeDeclaration)
    {
        dest.addEnumMember(x.name.getText(), x.initializer!=null ? " = " + x.initializer.getText() : "", this.getJsDoc(x.name));
    }

    private processHeritageClauseForClass(x:ts.HeritageClause, dest:HaxeTypeDeclaration)
    {
        switch (x.token)
        {
            case ts.SyntaxKind.ExtendsKeyword:
                dest.baseFullClassName = x.types.map(y => this.typeConvertor.convert(y, null)).toString();
                break;

            case ts.SyntaxKind.ImplementsKeyword:
                dest.baseFullInterfaceNames = x.types.map(y => this.typeConvertor.convert(y, null));
                break;
        }
    }

    private processPropertySignature(x:ts.PropertySignature, dest:HaxeTypeDeclaration)
    {
        this.logSubTree(x);

        this.addVarToHaxeTypeDeclaration(
            dest,
            this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null, dest.fullClassName + "@" + x.name.getText()),
            x.modifiers
        );
    }

    private processMethodSignature(x:ts.MethodSignature, dest:HaxeTypeDeclaration)
    {
        var methodName = x.name.getText();
        
        dest.addMethod
        (
            methodName,
            x.parameters.map(p => this.createVar(p.name.getText(), this.getParameterType(p), null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@" + methodName + "." + p.name.getText())),
            this.typeConvertor.convert(x.type, dest.fullClassName + "@" + methodName),
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.name),
            this.prepareTypeParameters(x)
        );
    }

    private processPropertyDeclaration(x:ts.PropertyDeclaration, dest:HaxeTypeDeclaration)
    {
        var varName = x.name.getText();
        this.addVarToHaxeTypeDeclaration(
            dest,
            this.createVar(varName, x.type, null, this.getJsDoc(x.name), x.questionToken != null, dest.fullClassName + "@" + varName),
            x.modifiers
        )
    }

    private processMethodDeclaration(x:ts.MethodDeclaration, dest:HaxeTypeDeclaration)
    {
        var methodName = x.name.getText();
        dest.addMethod
        (
            methodName,
            x.parameters.map(p => this.createVar(p.name.getText(), this.getParameterType(p), null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@" + methodName + "." + p.name.getText())),
            this.typeConvertor.convert(x.type, dest.fullClassName + "@" + methodName),
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.name),
            this.prepareTypeParameters(x)
        );
    }

    private processConstructor(x:ts.ConstructorDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addMethod
        (
            "new",
            x.parameters.map(p => this.createVar(p.name.getText(), this.getParameterType(p), null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@new." + p.name.getText())),
            "Void",
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.getFirstToken()),
            this.prepareTypeParameters(x)
        );
    }

    private processChildren(node:ts.Node, map:Map<number, (node:ts.Node) => void>)
    {
         ts.forEachChild(node, x =>
         {
             var f = map.get(x.kind);
             if (f)
             {
                 this.processNode(x, () => f(x));
             }
             else {
                this.logger.beginWarn();
                this.logger.log(this.indent + "vvvvv----IGNORE ----vvvvv");
                this.processNode(x, () => this.logSubTree(x));
                this.logger.log(this.indent + "^^^^^----IGNORE ----^^^^^");
                this.logger.endWarn();
             }
         });
    }

    private logSubTree(node: ts.Node)
    {
        ts.forEachChild(node, x =>
        {
           this.processNode(x, () => this.logSubTree(x));
        });
    }

    private processNode(node: ts.Node, callb:any)
    {
        this.logger.log(this.indent + this.tokens[node.kind] + ((<any>node).name && (<any>node).name.getText() ? " | " + (<any>node).name.getText() : ""));
        this.indent += "    ";
        callb();
        this.indent = this.indent.substring(0, this.indent.length - 4);
    }

    //private report(node: ts.Node, message: string)
    //{
    //    let obj = this.sourceFile.getLineAndCharacterOfPosition(node.getStart());
    //    this.logger.log(`${this.sourceFile.fileName} (${obj.line + 1},${obj.character + 1}): ${message}`);
    //}

    private addImports(moduleFilePath:string, ids:Array<string>)
    {
        for (let id of ids) this.imports.push(moduleFilePath.replace("/", ".") + "." + id);
    }

    private createVar(name:string, type:ts.Node, defaultValue:string, jsDoc:string, isOptional:boolean, typeLocalePath:string) : HaxeVar
    {
        return {
            haxeName: name,
            haxeType: this.typeConvertor.convert(type, typeLocalePath),
            haxeDefVal: defaultValue,
            jsDoc: jsDoc,
            isOptional: isOptional
        };
    }


    private getJsDoc(node:ts.Node)
    {
        var symbol = this.typeChecker.getSymbolAtLocation(node);
        return symbol ? ts.displayPartsToString(symbol.getDocumentationComment()) : "";
    }

    private isFlag(mods:{ flags:ts.NodeFlags }, f:ts.NodeFlags) : boolean
    {
        return mods && mods.flags && (mods.flags & f) !== 0;
    }

    private getHaxeTypeDeclarationByShort(type:"interface"|"class"|"enum"|"abstract"|"typedef"|"", shortClassName:string, native?:string) : HaxeTypeDeclaration
    {
        return this.getHaxeTypeDeclarationByFull(type, TypePathTools.makeFullClassPath([ this.curPackage, shortClassName ]), native);
    }

    private getHaxeTypeDeclarationByFull(type:"interface"|"class"|"enum"|"abstract"|"typedef"|"", fullClassName:string, native?:string)
    {
        var haxeType = this.allHaxeTypes.find(x => x.fullClassName == fullClassName);
        if (!haxeType)
        {
            haxeType = new HaxeTypeDeclaration(type, fullClassName);
            if (type != "interface" && type != "typedef")
            {
                let relativePackage = fullClassName.startsWith(this.rootPackage + ".") ? fullClassName.substring(this.rootPackage.length + 1) : fullClassName;
                haxeType.addMeta('@:native("' + (native ? native : TypePathTools.makeFullClassPath([ this.nativeNamespace, relativePackage ])) + '")');
            }
        }
        return haxeType;
    }

    private getModuleClass(node:ts.Node) : HaxeTypeDeclaration
    {
        let parts = this.curPackage.split(".");
        if (parts.length == 1 && parts[0] == "") parts[0] = "Root";
        else                                     parts[parts.length - 1] = TypePathTools.capitalize(parts[parts.length - 1]);
        
        let moduleName = parts.join(".");
        
        let curModuleClass = this.allHaxeTypes.find(x => x.fullClassName == moduleName);
        if (!curModuleClass)
        {
            curModuleClass = new HaxeTypeDeclaration("class", moduleName);
            var relativePackage = this.curPackage.startsWith(this.rootPackage + ".") ? this.curPackage.substring(this.rootPackage.length + 1) : "";
            curModuleClass.addMeta('@:native("' + TypePathTools.makeFullClassPath([ this.nativeNamespace, relativePackage ]) + '")');
            this.allHaxeTypes.push(curModuleClass);
        }

        return curModuleClass;
    }

    private prepareTypeParameters(node:{ typeParameters?:ts.NodeArray<ts.TypeParameterDeclaration> }) : Array<{ name:string, constraint:string }>
    {
        if (!node.typeParameters) return [];
        return node.typeParameters.map(t => ({ name:t.name.getText(), constraint:this.typeConvertor.convert(t.constraint, null) }))
    }

    private getClassNameFromPath(path:string) : string
    {
        var parts = path.split("\\").join("/").split("/");
        var r = parts.pop();
        if (r == "index.d.ts") r = parts.pop();
        r = r.split(".").join("-").split("-").map(x => StringTools.capitalize(x)).join("");
        return r;
    }

    private getParameterType(p: ts.ParameterDeclaration): ts.TypeNode
    {
        return p.dotDotDotToken ? this.avoidArray(p.type) : p.type;
    }

    private avoidArray(node: ts.TypeNode): ts.TypeNode  {
        switch (node.kind)
        {
            case ts.SyntaxKind.ArrayType:
                return (<ts.ArrayTypeNode>node).elementType;

            default:
                console.log("Can't avoid array for type '" + node.getFullText() + "'.");
                return node;
        }
    }

	public addVarToHaxeTypeDeclaration(t:HaxeTypeDeclaration, v:HaxeVar, modifiers:ts.ModifiersArray) : void
	{
		t.addVar(
			v,
			this.isFlag(modifiers, ts.NodeFlags.Private) || this.isFlag(modifiers, ts.NodeFlags.Protected),
			this.isFlag(modifiers, ts.NodeFlags.Static),
			this.isFlag(modifiers, ts.NodeFlags.Const) || this.isFlag(modifiers, ts.NodeFlags.Readonly)
		);
	}
}