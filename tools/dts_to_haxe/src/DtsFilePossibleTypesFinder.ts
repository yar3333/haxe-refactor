/// <reference path="../typings/globals/node/index.d.ts" />

import * as ts from "typescript";
import { Tokens } from "./Tokens";
import { TypePathTools } from "./TypePathTools";

export class DtsFilePossibleTypesFinder
{
    private tokens : string[];
    private allHaxeTypes: Array<string>;
    private curPackage: string;
    
    constructor(private sourceFile: ts.SourceFile, private rootPackage:string)
    {
        this.tokens = Tokens.getAll();
    }

    public find() : Array<string>
    {
        this.allHaxeTypes = [];
        this.curPackage = this.rootPackage;

        const node = this.sourceFile;

        var savePack = this.curPackage;

        ts.forEachChild(node, x => {
            if (x.kind == ts.SyntaxKind.NamespaceExportDeclaration) {
                this.curPackage = TypePathTools.makeFullClassPath([ this.curPackage, (<ts.NamespaceExportDeclaration>x).name.getText() ]);
            }
        });

        this.processChildren(node, new Map<number, (node:any) => void>(
        [
            [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
            [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
            [ ts.SyntaxKind.EnumDeclaration, (x:ts.EnumDeclaration) => this.processEnumDeclaration(x) ],
            [ ts.SyntaxKind.ModuleDeclaration, (x:ts.ModuleDeclaration) => this.processModuleDeclaration(x) ],
            [ ts.SyntaxKind.NamespaceExportDeclaration, (x:ts.NamespaceExportDeclaration) => {} ],
            [ ts.SyntaxKind.EndOfFileToken, (x) => {} ]
        ]));

        this.curPackage = savePack;

        return this.allHaxeTypes;
    }

    private processModuleDeclaration(node:ts.ModuleDeclaration)
    {
        var savePack = this.curPackage;
        
        this.curPackage = TypePathTools.makeFullClassPath([ this.curPackage, node.name.getText() ]);

        this.allHaxeTypes.push(TypePathTools.normalizeFullClassName(this.curPackage));
        
        this.processChildren(node.body, new Map<number, (node:ts.Node) => void>(
        [
            [ ts.SyntaxKind.ModuleDeclaration, (x:ts.ModuleDeclaration) => this.processModuleDeclaration(x) ],
            [ ts.SyntaxKind.ModuleBlock, (x:ts.ModuleBlock) => this.processModuleBlock(x) ],
            [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
            [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
        ]));

        this.curPackage = savePack;
    }
    
    private processModuleBlock(node:ts.ModuleBlock)
    {
        this.processChildren(node, new Map<number, (node:ts.Node) => void>(
        [
            [ ts.SyntaxKind.InterfaceDeclaration, (x:ts.InterfaceDeclaration) => this.processInterfaceDeclaration(x) ],
            [ ts.SyntaxKind.ClassDeclaration, (x:ts.ClassDeclaration) => this.processClassDeclaration(x) ],
            [ ts.SyntaxKind.EnumDeclaration, (x:ts.EnumDeclaration) => this.processEnumDeclaration(x) ],
        ]));
    }
    
    private processInterfaceDeclaration(node:ts.InterfaceDeclaration)
    {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }

    private processClassDeclaration(node:ts.ClassDeclaration)
    {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }
    
    private processEnumDeclaration(node:ts.EnumDeclaration)
    {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }

    private processChildren(node:ts.Node, map:Map<number, (node:ts.Node) => void>)
    {
         ts.forEachChild(node, x =>
         {
             var f = map.get(x.kind);
             if (f)
             {
                 f(x);
             }
         });
    }

    private getHaxeTypeFullNameByShort(shortClassName:string) : string
    {
        return TypePathTools.normalizeFullClassName(TypePathTools.makeFullClassPath([ this.curPackage, shortClassName ]));
    }
}