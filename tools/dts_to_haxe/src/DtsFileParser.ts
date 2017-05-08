/// <reference path="../typings/globals/node/index.d.ts" />

import { basename } from "path";
import * as ts from "typescript";
import { Tokens } from "./Tokens";
import { TsToHaxeStdTypes } from "./TsToHaxeStdTypes";
import { HaxeTypeDeclaration, HaxeVar } from "./HaxeTypeDeclaration";
import { ILogger } from "./ILogger";

export class DtsFileParser
{
    private logger : ILogger;

    private tokens : string[];
    private typeMapper : Map<string, string>;
    private indent = "";

    private imports = new Array<string>();
    public allHaxeTypes: Array<HaxeTypeDeclaration>;

    private curPackage: string;

    private curModuleClass: HaxeTypeDeclaration = null;

    constructor(private sourceFile: ts.SourceFile, private typeChecker: ts.TypeChecker, private rootPackage:string, private nativeNamespace:string)
    {
        this.tokens = Tokens.getAll();
        this.typeMapper = TsToHaxeStdTypes.getAll();
    }

    public parse(allHaxeTypes:Array<HaxeTypeDeclaration>, logger:ILogger) : void
    {
        this.allHaxeTypes = allHaxeTypes;
        this.logger = logger;
        this.curPackage = this.rootPackage;

        const node = this.sourceFile;

        var savePack = this.curPackage;

        ts.forEachChild(node, x => {
            if (x.kind == ts.SyntaxKind.NamespaceExportDeclaration) {
                this.curPackage = this.makeFullClassPath([ this.curPackage, (<ts.NamespaceExportDeclaration>x).name.getText() ]);
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

    private processModuleDeclaration(node:ts.ModuleDeclaration)
    {
        var savePack = this.curPackage;
        
        this.curPackage = this.makeFullClassPath([ this.curPackage, node.name.getText() ]);
        
        this.processChildren(node.body, new Map<number, (node:ts.Node) => void>(
        [
            [ ts.SyntaxKind.ModuleDeclaration, (x:ts.ModuleDeclaration) => this.processModuleDeclaration(x) ],
            [ ts.SyntaxKind.ModuleBlock, (x:ts.ModuleBlock) => this.processModuleBlock(x) ],
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
        if (!this.isFlag(node, ts.NodeFlags.Export)) return;

        for (var decl of node.declarationList.declarations)
        {
            this.logger.log(this.indent + "| " + decl.name.getText())
            var isReadOnly = this.isFlag(node.declarationList, ts.NodeFlags.Const) || this.isFlag(node.declarationList, ts.NodeFlags.Readonly);
            this.getModuleClass(node).addVar(this.createVar(decl.name.getText(), decl.type, null, this.getJsDoc(decl.name), false), false, true, isReadOnly);
        }
    }
    
    private processFunctionDeclaration(node:ts.FunctionDeclaration)
    {
        if (!this.isFlag(node, ts.NodeFlags.Export)) return;

        this.logger.log(this.indent + "| " + node.name.getText())

        this.getModuleClass(node).addMethod
        (
            node.name.getText(),
            node.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), node.questionToken != null)),
            this.convertType(node.type),
            null,
            false, // private
            true, // static
            this.getJsDoc(node.name)
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
        var item = this.getHaxeTypeDeclarationByShort("interface", node.name.getText());
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => item.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([ this.curPackage, y.getText() ])) ],
            [ ts.SyntaxKind.PropertySignature, (x:ts.PropertySignature) => this.processPropertySignature(x, item) ],
            [ ts.SyntaxKind.MethodSignature, (x:ts.MethodSignature) => this.processMethodSignature(x, item) ]
        ]));

        this.allHaxeTypes.push(item);
    }

    private processClassDeclaration(node:ts.ClassDeclaration)
    {
        var item = this.getHaxeTypeDeclarationByShort("class", node.name.getText());
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => this.processHeritageClauseForClass(x, item) ],
            [ ts.SyntaxKind.PropertyDeclaration, (x:ts.PropertyDeclaration) => this.processPropertyDeclaration(x, item) ],
            [ ts.SyntaxKind.MethodDeclaration, (x:ts.MethodDeclaration) => this.processMethodDeclaration(x, item) ],
            [ ts.SyntaxKind.Constructor, (x:ts.ConstructorDeclaration) => this.processConstructor(x, item) ]
        ]));

        this.allHaxeTypes.push(item);
    }

    private processEnumDeclaration(node:ts.EnumDeclaration)
    {
        var item = this.getHaxeTypeDeclarationByShort("enum", node.name.getText());
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => {} ],
            [ ts.SyntaxKind.EnumMember, (x:ts.EnumMember) => this.processEnumMember(x, item) ],
        ]));

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
                dest.baseFullClassName = x.types.map(y => this.makeFullClassPath([ this.curPackage, y.getText() ])).toString();
                break;

            case ts.SyntaxKind.ImplementsKeyword:
                dest.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([ this.curPackage, y.getText() ]));
                break;
        }
    }

    private processPropertySignature(x:ts.PropertySignature, dest:HaxeTypeDeclaration)
    {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null));
    }

    private processMethodSignature(x:ts.MethodSignature, dest:HaxeTypeDeclaration)
    {
        dest.addMethod
        (
            x.name.getText(),
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), x.questionToken != null)),
            this.convertType(x.type),
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.name)
        );
    }

    private processPropertyDeclaration(x:ts.PropertyDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null));
    }

    private processMethodDeclaration(x:ts.MethodDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addMethod(
            x.name.getText(),
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), x.questionToken != null)),
            this.convertType(x.type),
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.name)
        );
    }

    private processConstructor(x:ts.ConstructorDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addMethod(
            "new",
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), p.questionToken != null)),
            "Void",
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.getFirstToken())
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

    private createVar(name:string, type:ts.Node, defaultValue:string, jsDoc:string, isOptional:boolean) : HaxeVar
    {
        return {
            haxeName: name,
            haxeType: this.convertType(type),
            haxeDefVal: defaultValue,
            jsDoc: jsDoc,
            isOptional: isOptional
        };
    }

    private convertType(node:ts.Node) : string
    {
        if (!node) return "Dynamic";

        switch (node.kind)
        {
            case ts.SyntaxKind.FunctionType:
            {
                let t = <ts.FunctionTypeNode>node;
                let types = [];
                for (var p of t.parameters) types.push(this.convertType(p.type));
                types.push(this.convertType(t.type));
                return types.join("->");
            }

            case ts.SyntaxKind.ArrayType:
            {
                let t = <ts.ArrayTypeNode>node;
                return "Array<" + this.convertType(t.elementType) + ">";
            }
            
            case ts.SyntaxKind.UnionType:
            {
                return this.convertUnionType((<ts.UnionTypeNode>node).types);
            }
            
            case ts.SyntaxKind.TypeLiteral:
            {
                return this.processTypeLiteral(<ts.TypeLiteralNode>node);
            }
        }

        var s = node.getText();
        return this.typeMapper.get(s) ? this.typeMapper.get(s) : s;
    }

    private convertUnionType(types:Array<ts.TypeNode>) : string
    {
        if (types.length == 1) return this.convertType(types[0]);
        return "haxe.extern.EitherType<" + this.convertType(types[0])+", " +  this.convertUnionType(types.slice(1)) + ">";
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

    private capitalize(s:string) : string
    {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    private makeFullClassPath(parts:Array<string>) : string
    {
        var s = "";
        for (var p of parts)
        {
            if (p !== null && p !== "" && s != "") s += ".";
            s += p;
        }
        return s;
    }

    private processTypeLiteral(node:ts.TypeLiteralNode) : string
    {
        if (node.members.length == 1 && node.members[0].kind == ts.SyntaxKind.IndexSignature)
        {
            let tt = <ts.IndexSignatureDeclaration>node.members[0];
            if (tt.parameters.length == 1) return "Dynamic<" + this.convertType(tt.type) + ">";
        }
    
       var item = new HaxeTypeDeclaration("");
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.PropertySignature, (x:ts.PropertySignature) => this.processPropertySignature(x, item) ],
            [ ts.SyntaxKind.MethodSignature, (x:ts.MethodSignature) => this.processMethodSignature(x, item) ]
        ]));

        return item.toString();
    }

    private getHaxeTypeDeclarationByShort(type:"interface"|"class"|"enum"|"", shortClassName:string) : HaxeTypeDeclaration
    {
        return this.getHaxeTypeDeclarationByFull(type, this.makeFullClassPath([ this.curPackage, shortClassName ]));
    }

    private getHaxeTypeDeclarationByFull(type:"interface"|"class"|"enum"|"", fullClassName:string)
    {
        var haxeType = this.allHaxeTypes.find(x => x.fullClassName == fullClassName);
        if (!haxeType)
        {
            haxeType = new HaxeTypeDeclaration(type, fullClassName);
            let relativePackage = fullClassName.startsWith(this.rootPackage + ".") ? fullClassName.substring(this.rootPackage.length + 1) : "";
            haxeType.addMeta('@:native("' + this.makeFullClassPath([ this.nativeNamespace, relativePackage ]) + '")');
        }
        return haxeType;
    }

    private getModuleClass(node:ts.Node) : HaxeTypeDeclaration
    {
        if (this.curModuleClass == null)
        {
        let parts = this.curPackage.split(".");
        if (parts.length == 1 && parts[0] == "") parts[0] = "Root";
            else                                     parts[parts.length - 1] = this.capitalize(parts[parts.length - 1]);
        
        let moduleName = parts.join(".");
        
            this.curModuleClass = new HaxeTypeDeclaration("class", moduleName);
            var relativePackage = this.curPackage.startsWith(this.rootPackage + ".") ? this.curPackage.substring(this.rootPackage.length + 1) : "";
            this.curModuleClass.addMeta('@:native("' + this.makeFullClassPath([ this.nativeNamespace, relativePackage ]) + '")');
            this.allHaxeTypes.push(this.curModuleClass);
        }
        return this.curModuleClass;
    }
}