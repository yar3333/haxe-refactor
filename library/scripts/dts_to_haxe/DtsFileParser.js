/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const path_1 = require("path");
const ts = require("typescript");
const Tokens_1 = require("./Tokens");
const TsToHaxeStdTypes_1 = require("./TsToHaxeStdTypes");
const HaxeTypeDeclaration_1 = require("./HaxeTypeDeclaration");
class DtsFileParser {
    constructor(sourceFile, typeChecker, rootPackage, nativeNamespace) {
        this.sourceFile = sourceFile;
        this.typeChecker = typeChecker;
        this.rootPackage = rootPackage;
        this.nativeNamespace = nativeNamespace;
        this.indent = "";
        this.imports = new Array();
        this.classesAndInterfaces = new Array();
        this.tokens = Tokens_1.Tokens.getAll();
        this.typeMapper = TsToHaxeStdTypes_1.TsToHaxeStdTypes.getAll();
    }
    parse() {
        const node = this.sourceFile;
        this.processNode(node, () => {
            switch (node.kind) {
                case ts.SyntaxKind.SourceFile:
                    this.processChildren(node, new Map([
                        [ts.SyntaxKind.ImportDeclaration, (x) => this.processImportDeclaration(x)],
                        [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
                        [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
                        [ts.SyntaxKind.VariableStatement, (x) => this.processVariableStatement(x)],
                        [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
                        [ts.SyntaxKind.EndOfFileToken, (x) => { }]
                    ]));
                    break;
                default:
                    console.log(this.indent + "^----- UNKNOW ROOT ELEMENT");
                    this.logSubTree(node);
            }
        });
        return this.classesAndInterfaces;
    }
    processVariableStatement(node) {
        if (!this.isFlag(node, ts.NodeFlags.Export))
            return;
        for (var decl of node.declarationList.declarations) {
            var isReadOnly = this.isFlag(node.declarationList, ts.NodeFlags.Const) || this.isFlag(node.declarationList, ts.NodeFlags.Readonly);
            this.getModuleClass(node).addVar(this.createVar(decl.name.getText(), decl.type, null, this.getJsDoc(decl.name), false), false, true, isReadOnly);
        }
    }
    processImportDeclaration(node) {
        var ids = new Array();
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ImportClause, (x) => {
                    this.processChildren(x, new Map([
                        [ts.SyntaxKind.NamedImports, (y) => {
                                this.processChildren(y, new Map([
                                    [ts.SyntaxKind.ImportSpecifier, (z) => {
                                            this.processChildren(z, new Map([
                                                [ts.SyntaxKind.Identifier, (t) => {
                                                        ids.push(t.text);
                                                    }]
                                            ]));
                                        }]
                                ]));
                            }]
                    ]));
                }],
            [ts.SyntaxKind.StringLiteral, (x) => {
                    this.addImports(x.text, ids);
                }]
        ]));
    }
    processInterfaceDeclaration(node) {
        var item = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("interface");
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => this.processTypeDeclarationIdentifier(x, item)],
            [ts.SyntaxKind.HeritageClause, (x) => item.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([this.rootPackage, y.getText()]))],
            [ts.SyntaxKind.PropertySignature, (x) => this.processPropertySignature(x, item)],
            [ts.SyntaxKind.MethodSignature, (x) => this.processMethodSignature(x, item)]
        ]));
        this.classesAndInterfaces.push(item);
    }
    processClassDeclaration(node) {
        var item = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("class");
        item.docComment = this.getJsDoc(node.name);
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => this.processTypeDeclarationIdentifier(x, item)],
            [ts.SyntaxKind.HeritageClause, (x) => this.processHeritageClauseForClass(x, item)],
            [ts.SyntaxKind.PropertyDeclaration, (x) => this.processPropertyDeclaration(x, item)],
            [ts.SyntaxKind.MethodDeclaration, (x) => this.processMethodDeclaration(x, item)],
            [ts.SyntaxKind.Constructor, (x) => this.processConstructor(x, item)]
        ]));
        this.classesAndInterfaces.push(item);
    }
    processEnumDeclaration(node) {
        var item = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("enum");
        item.docComment = this.getJsDoc(node.name);
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => this.processTypeDeclarationIdentifier(x, item)],
            [ts.SyntaxKind.EnumMember, (x) => this.processEnumMember(x, item)],
        ]));
        this.classesAndInterfaces.push(item);
    }
    processEnumMember(x, dest) {
        dest.addEnumMember(x.name.getText(), x.initializer != null ? " = " + x.initializer.getText() : "", this.getJsDoc(x.name));
    }
    processHeritageClauseForClass(x, dest) {
        switch (x.token) {
            case ts.SyntaxKind.ExtendsKeyword:
                dest.baseFullClassName = x.types.map(y => this.makeFullClassPath([this.rootPackage, y.getText()])).toString();
                break;
            case ts.SyntaxKind.ImplementsKeyword:
                dest.baseFullInterfaceNames = x.types.map(y => this.makeFullClassPath([this.rootPackage, y.getText()]));
                break;
        }
    }
    processPropertySignature(x, dest) {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null));
    }
    processMethodSignature(x, dest) {
        dest.addMethod(x.name.getText(), x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), x.questionToken != null)), this.convertType(x.type), null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.name));
    }
    processPropertyDeclaration(x, dest) {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null));
    }
    processMethodDeclaration(x, dest) {
        dest.addMethod(x.name.getText(), x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), x.questionToken != null)), this.convertType(x.type), null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.name));
    }
    processConstructor(x, dest) {
        dest.addMethod("new", x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), p.questionToken != null)), "Void", null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.getFirstToken()));
    }
    processChildren(node, map) {
        ts.forEachChild(node, x => {
            var f = map.get(x.kind);
            if (f) {
                this.processNode(x, () => f(x));
            }
            else {
                console.log(this.indent + "vvvvv----IGNORE ----vvvvv");
                this.processNode(x, () => this.logSubTree(x));
                console.log(this.indent + "^^^^^----IGNORE ----^^^^^");
            }
        });
    }
    logSubTree(node) {
        ts.forEachChild(node, x => {
            this.processNode(x, () => this.logSubTree(x));
        });
    }
    processNode(node, callb) {
        console.log(this.indent + this.tokens[node.kind]);
        this.indent += "    ";
        callb();
        this.indent = this.indent.substring(0, this.indent.length - 4);
    }
    report(node, message) {
        let obj = this.sourceFile.getLineAndCharacterOfPosition(node.getStart());
        console.log(`${this.sourceFile.fileName} (${obj.line + 1},${obj.character + 1}): ${message}`);
    }
    addImports(moduleFilePath, ids) {
        for (let id of ids)
            this.imports.push(moduleFilePath.replace("/", ".") + "." + id);
    }
    createVar(name, type, defaultValue, jsDoc, isOptional) {
        return {
            haxeName: name,
            haxeType: this.convertType(type),
            haxeDefVal: defaultValue,
            jsDoc: jsDoc,
            isOptional: isOptional
        };
    }
    convertType(node) {
        if (!node)
            return "Dynamic";
        switch (node.kind) {
            case ts.SyntaxKind.FunctionType:
                {
                    let t = node;
                    let types = [];
                    for (var p of t.parameters)
                        types.push(this.convertType(p.type));
                    types.push(this.convertType(t.type));
                    return types.join("->");
                }
            case ts.SyntaxKind.ArrayType:
                {
                    let t = node;
                    return "Array<" + this.convertType(t.elementType) + ">";
                }
            case ts.SyntaxKind.UnionType:
                {
                    return this.convertUnionType(node.types);
                }
            case ts.SyntaxKind.TypeLiteral:
                {
                    return this.processTypeLiteral(node);
                }
        }
        var s = node.getText();
        return this.typeMapper.get(s) ? this.typeMapper.get(s) : s;
    }
    convertUnionType(types) {
        if (types.length == 1)
            return this.convertType(types[0]);
        return "haxe.extern.EitherType<" + this.convertType(types[0]) + ", " + this.convertUnionType(types.slice(1)) + ">";
    }
    getJsDoc(node) {
        var symbol = this.typeChecker.getSymbolAtLocation(node);
        return symbol ? ts.displayPartsToString(symbol.getDocumentationComment()) : "";
    }
    isFlag(mods, f) {
        return mods && mods.flags && (mods.flags & f) !== 0;
    }
    capitalize(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
    getModuleClass(node) {
        var moduleName = this.makeFullClassPath([this.rootPackage, this.capitalize(path_1.basename(node.getSourceFile().fileName, ".d.ts"))]);
        var moduleClass = this.classesAndInterfaces.find(x => x.fullClassName == moduleName);
        if (!moduleClass) {
            moduleClass = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("class", moduleName);
            moduleClass.addMeta('@:native("' + this.makeFullClassPath([this.nativeNamespace, moduleName]) + '")');
            this.classesAndInterfaces.push(moduleClass);
        }
        return moduleClass;
    }
    makeFullClassPath(parts) {
        var s = "";
        for (var p of parts) {
            if (p !== null && p !== "" && s != "")
                s += ".";
            s += p;
        }
        return s;
    }
    processTypeDeclarationIdentifier(x, dest) {
        dest.fullClassName = this.makeFullClassPath([this.rootPackage, x.text]);
        dest.addMeta('@:native("' + this.makeFullClassPath([this.nativeNamespace, x.text]) + '")');
    }
    processTypeLiteral(node) {
        if (node.members.length == 1 && node.members[0].kind == ts.SyntaxKind.IndexSignature) {
            let tt = node.members[0];
            if (tt.parameters.length == 1)
                return "Dynamic<" + this.convertType(tt.type) + ">";
        }
        var item = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("");
        this.processChildren(node, new Map([
            [ts.SyntaxKind.PropertySignature, (x) => this.processPropertySignature(x, item)],
            [ts.SyntaxKind.MethodSignature, (x) => this.processMethodSignature(x, item)]
        ]));
        return item.toString();
    }
}
exports.DtsFileParser = DtsFileParser;
//# sourceMappingURL=DtsFileParser.js.map