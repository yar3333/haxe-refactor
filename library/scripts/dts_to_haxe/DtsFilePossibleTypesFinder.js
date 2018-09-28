/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const ts = require("typescript");
const Tokens_1 = require("./Tokens");
const TypePathTools_1 = require("./TypePathTools");
class DtsFilePossibleTypesFinder {
    constructor(sourceFile, rootPackage) {
        this.sourceFile = sourceFile;
        this.rootPackage = rootPackage;
        this.tokens = Tokens_1.Tokens.getAll();
    }
    find() {
        this.allHaxeTypes = [];
        this.curPackage = this.rootPackage;
        const node = this.sourceFile;
        var savePack = this.curPackage;
        ts.forEachChild(node, x => {
            if (x.kind == ts.SyntaxKind.NamespaceExportDeclaration) {
                this.curPackage = TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, x.name.getText()]);
            }
        });
        this.processChildren(node, new Map([
            [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
            [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
            [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
            [ts.SyntaxKind.TypeAliasDeclaration, (x) => this.processTypeAliasDeclaration(x)],
            [ts.SyntaxKind.ModuleDeclaration, (x) => this.processModuleDeclaration(x)],
            [ts.SyntaxKind.NamespaceExportDeclaration, (x) => { }],
            [ts.SyntaxKind.EndOfFileToken, (x) => { }]
        ]));
        this.curPackage = savePack;
        return this.allHaxeTypes;
    }
    processModuleDeclaration(node) {
        var savePack = this.curPackage;
        this.curPackage = TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, node.name.getText()]);
        this.allHaxeTypes.push(TypePathTools_1.TypePathTools.normalizeFullClassName(this.curPackage));
        this.processChildren(node.body, new Map([
            [ts.SyntaxKind.ModuleDeclaration, (x) => this.processModuleDeclaration(x)],
            [ts.SyntaxKind.ModuleBlock, (x) => this.processModuleBlock(x)],
            [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
            [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
            [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
            [ts.SyntaxKind.TypeAliasDeclaration, (x) => this.processTypeAliasDeclaration(x)],
        ]));
        this.curPackage = savePack;
    }
    processModuleBlock(node) {
        this.processChildren(node, new Map([
            [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
            [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
            [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
            [ts.SyntaxKind.TypeAliasDeclaration, (x) => this.processTypeAliasDeclaration(x)],
        ]));
    }
    processInterfaceDeclaration(node) {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }
    processClassDeclaration(node) {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }
    processEnumDeclaration(node) {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }
    processTypeAliasDeclaration(node) {
        this.allHaxeTypes.push(this.getHaxeTypeFullNameByShort(node.name.getText()));
    }
    processChildren(node, map) {
        ts.forEachChild(node, x => {
            var f = map.get(x.kind);
            if (f) {
                f(x);
            }
        });
    }
    getHaxeTypeFullNameByShort(shortClassName) {
        return TypePathTools_1.TypePathTools.normalizeFullClassName(TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, shortClassName]));
    }
}
exports.DtsFilePossibleTypesFinder = DtsFilePossibleTypesFinder;
//# sourceMappingURL=DtsFilePossibleTypesFinder.js.map