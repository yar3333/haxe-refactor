/// <reference path="../typings/globals/node/index.d.ts" />

import { basename } from "path";
import * as ts from "typescript";
import { Tokens } from "./Tokens";
import { TsToHaxeStdTypes } from "./TsToHaxeStdTypes";
import { HaxeTypeDeclaration, HaxeVar } from "./HaxeTypeDeclaration";

export class DtsFileParser
{
    private tokens : string[];
    private typeMapper : Map<string, string>;
    private indent = "";

    private imports = new Array<string>();
    private classesAndInterfaces = new Array<HaxeTypeDeclaration>();

    constructor(private sourceFile: ts.SourceFile, private typeChecker: ts.TypeChecker, private rootPackage:string, private nativeNamespace:string)
    {
        this.tokens = Tokens.getAll();
        this.typeMapper = TsToHaxeStdTypes.getAll();
    }

    public parse() : Array<HaxeTypeDeclaration>
    {
        const node = this.sourceFile;

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
                        [ ts.SyntaxKind.EndOfFileToken, (x) => {} ]
                    ]));
                    break;

                default:
                    console.log(this.indent + "^----- UNKNOW ROOT ELEMENT");
                    this.logSubTree(node);
            }
        });

        return this.classesAndInterfaces;
    }
    
    private processVariableStatement(node:ts.VariableStatement)
    {
        if (!this.isFlag(node, ts.NodeFlags.Export)) return;
        
        for (var decl of node.declarationList.declarations)
        {
            var isReadOnly = this.isFlag(node.declarationList, ts.NodeFlags.Const) || this.isFlag(node.declarationList, ts.NodeFlags.Readonly);
            this.getModuleClass(node).addVar(this.createVar(decl.name.getText(), decl.type, null, this.getJsDoc(decl.name)), false, true, isReadOnly);
        }
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
        var item = new HaxeTypeDeclaration("interface");
        
        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => this.processTypeDeclarationIdentifier(x, item) ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => item.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([ this.rootPackage, y.getText() ])) ],
            [ ts.SyntaxKind.PropertySignature, (x:ts.PropertySignature) => this.processPropertySignature(x, item) ],
            [ ts.SyntaxKind.MethodSignature, (x:ts.MethodSignature) => this.processMethodSignature(x, item) ]
        ]));

        this.classesAndInterfaces.push(item);
    }

    private processClassDeclaration(node:ts.ClassDeclaration)
    {
        var item = new HaxeTypeDeclaration("class");
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => this.processTypeDeclarationIdentifier(x, item) ],
            [ ts.SyntaxKind.HeritageClause, (x:ts.HeritageClause) => this.processHeritageClauseForClass(x, item) ],
            [ ts.SyntaxKind.PropertyDeclaration, (x:ts.PropertyDeclaration) => this.processPropertyDeclaration(x, item) ],
            [ ts.SyntaxKind.MethodDeclaration, (x:ts.MethodDeclaration) => this.processMethodDeclaration(x, item) ],
            [ ts.SyntaxKind.Constructor, (x:ts.ConstructorDeclaration) => this.processConstructor(x, item) ]
        ]));

        this.classesAndInterfaces.push(item);
    }

    private processEnumDeclaration(node:ts.EnumDeclaration)
    {
        var item = new HaxeTypeDeclaration("enum");
        
        item.docComment = this.getJsDoc(node.name);

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.ExportKeyword, (x) => {} ],
            [ ts.SyntaxKind.Identifier, (x:ts.Identifier) => this.processTypeDeclarationIdentifier(x, item) ],
            [ ts.SyntaxKind.EnumMember, (x:ts.EnumMember) => this.processEnumMember(x, item) ],
        ]));

        this.classesAndInterfaces.push(item);
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
                dest.baseFullClassName = x.types.map(y => this.makeFullClassPath([ this.rootPackage, y.getText() ])).toString();
                break;

            case ts.SyntaxKind.ImplementsKeyword:
                dest.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([ this.rootPackage, y.getText() ]));
                break;
        }
    }

    private processPropertySignature(x:ts.PropertySignature, dest:HaxeTypeDeclaration)
    {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name)));
    }

    private processMethodSignature(x:ts.MethodSignature, dest:HaxeTypeDeclaration)
    {
        dest.addMethod(
            x.name.getText(),
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name))),
            this.convertType(x.type),
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.name)
        );
    }

    private processPropertyDeclaration(x:ts.PropertyDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name)));
    }

    private processMethodDeclaration(x:ts.MethodDeclaration, dest:HaxeTypeDeclaration)
    {
        dest.addMethod(
            x.name.getText(),
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name))),
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
            x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name))),
            "Void",
            null,
            this.isFlag(x.modifiers, ts.NodeFlags.Private),
            this.isFlag(x.modifiers, ts.NodeFlags.Static),
            this.getJsDoc(x.getFirstToken())
        );
    }

    private processChildren(node:ts.Node, map:Map<number, (node:any) => void>)
    {
         ts.forEachChild(node, x =>
         {
             var f = map.get(x.kind);
             if (f)
             {
                 this.processNode(x, () => f(x));
             }
             else {
                console.log(this.indent + "vvvvv----IGNORE ----vvvvv");
                this.processNode(x, () => this.logSubTree(x));
                console.log(this.indent + "^^^^^----IGNORE ----^^^^^");
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
        console.log(this.indent + this.tokens[node.kind]);
        this.indent += "    ";
        callb();
        this.indent = this.indent.substring(0, this.indent.length - 4);
    }

    private report(node: ts.Node, message: string)
    {
        let obj = this.sourceFile.getLineAndCharacterOfPosition(node.getStart());
        console.log(`${this.sourceFile.fileName} (${obj.line + 1},${obj.character + 1}): ${message}`);
    }

    private addImports(moduleFilePath:string, ids:Array<string>)
    {
        for (let id of ids) this.imports.push(moduleFilePath.replace("/", ".") + "." + id);
    }

    private createVar(name:string, type:ts.Node, defaultValue:string, jsDoc:string) : HaxeVar
    {
        return {
            haxeName: name,
            haxeType: this.convertType(type),
            haxeDefVal: defaultValue,
            jsDoc: jsDoc
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
            
            default:
                var s = node.getText();
                return this.typeMapper.get(s) ? this.typeMapper.get(s) : s;
        }
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

    private getModuleClass(node:ts.Node) : HaxeTypeDeclaration
    {
        var moduleName = this.makeFullClassPath([ this.rootPackage, this.capitalize(basename(node.getSourceFile().fileName, ".d.ts")) ]);
        var moduleClass = this.classesAndInterfaces.find(x => x.fullClassName == moduleName);
        if (!moduleClass)
        {
            moduleClass = new HaxeTypeDeclaration("class", moduleName);
            moduleClass.addMeta('@:native("' + this.makeFullClassPath([ this.nativeNamespace, moduleName ]) + '")');
            this.classesAndInterfaces.push(moduleClass);
        }
        return moduleClass;
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

    private processTypeDeclarationIdentifier(x:ts.Identifier, dest:HaxeTypeDeclaration)
    {
        dest.fullClassName = this.makeFullClassPath([ this.rootPackage, x.text ]);
        dest.addMeta('@:native("' + this.makeFullClassPath([ this.nativeNamespace, x.text ]) + '")');
    }
}