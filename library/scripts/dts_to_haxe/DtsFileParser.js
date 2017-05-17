/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const ts = require("typescript");
const Tokens_1 = require("./Tokens");
const HaxeTypeDeclaration_1 = require("./HaxeTypeDeclaration");
const TypePathTools_1 = require("./TypePathTools");
class DtsFileParser {
    constructor(sourceFile, typeChecker, typeConvertor, rootPackage, nativeNamespace, typedefs, knownTypes) {
        this.sourceFile = sourceFile;
        this.typeChecker = typeChecker;
        this.typeConvertor = typeConvertor;
        this.rootPackage = rootPackage;
        this.nativeNamespace = nativeNamespace;
        this.typedefs = typedefs;
        this.knownTypes = knownTypes;
        this.indent = "";
        this.imports = new Array();
        this.tokens = Tokens_1.Tokens.getAll();
    }
    parse(allHaxeTypes, logger) {
        this.allHaxeTypes = allHaxeTypes;
        this.logger = logger;
        this.curPackage = this.rootPackage;
        const node = this.sourceFile;
        var savePack = this.curPackage;
        ts.forEachChild(node, x => {
            if (x.kind == ts.SyntaxKind.NamespaceExportDeclaration) {
                this.curPackage = TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, x.name.getText()]);
            }
        });
        this.processNode(node, () => {
            switch (node.kind) {
                case ts.SyntaxKind.SourceFile:
                    this.processChildren(node, new Map([
                        [ts.SyntaxKind.ImportDeclaration, (x) => this.processImportDeclaration(x)],
                        [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
                        [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
                        [ts.SyntaxKind.VariableStatement, (x) => this.processVariableStatement(x)],
                        [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
                        [ts.SyntaxKind.FunctionDeclaration, (x) => this.processFunctionDeclaration(x)],
                        [ts.SyntaxKind.ModuleDeclaration, (x) => this.processModuleDeclaration(x)],
                        [ts.SyntaxKind.NamespaceExportDeclaration, (x) => { }],
                        [ts.SyntaxKind.EndOfFileToken, (x) => { }]
                    ]));
                    break;
                default:
                    this.logger.log(this.indent + "^----- UNKNOW ROOT ELEMENT");
                    this.logSubTree(node);
            }
        });
        this.curPackage = savePack;
    }
    processModuleDeclaration(node) {
        var savePack = this.curPackage;
        this.curPackage = TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, node.name.getText()]);
        this.processChildren(node.body, new Map([
            [ts.SyntaxKind.ModuleDeclaration, (x) => this.processModuleDeclaration(x)],
            [ts.SyntaxKind.ModuleBlock, (x) => this.processModuleBlock(x)],
            [ts.SyntaxKind.FunctionDeclaration, (x) => this.processFunctionDeclaration(x)],
            [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
            [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
            [ts.SyntaxKind.VariableStatement, (x) => this.processVariableStatement(x)],
        ]));
        this.curPackage = savePack;
    }
    processModuleBlock(node) {
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, x => { }],
            [ts.SyntaxKind.Identifier, x => { }],
            [ts.SyntaxKind.InterfaceDeclaration, (x) => this.processInterfaceDeclaration(x)],
            [ts.SyntaxKind.ClassDeclaration, (x) => this.processClassDeclaration(x)],
            [ts.SyntaxKind.VariableStatement, (x) => this.processVariableStatement(x)],
            [ts.SyntaxKind.EnumDeclaration, (x) => this.processEnumDeclaration(x)],
            [ts.SyntaxKind.FunctionDeclaration, (x) => this.processFunctionDeclaration(x)],
        ]));
    }
    processVariableStatement(node) {
        if (!this.isFlag(node, ts.NodeFlags.Export))
            return;
        for (var decl of node.declarationList.declarations) {
            this.logger.log(this.indent + "| " + decl.name.getText());
            var isReadOnly = this.isFlag(node.declarationList, ts.NodeFlags.Const) || this.isFlag(node.declarationList, ts.NodeFlags.Readonly);
            var klass = this.getModuleClass(node);
            var varName = decl.name.getText();
            klass.addVar(this.createVar(varName, decl.type, null, this.getJsDoc(decl.name), false, klass.fullClassName + "@" + varName), false, true, isReadOnly);
        }
    }
    processFunctionDeclaration(node) {
        if (!this.isFlag(node, ts.NodeFlags.Export))
            return;
        this.logger.log(this.indent + "| " + node.name.getText());
        var item = this.getModuleClass(node);
        var methodName = node.name.getText();
        item.addMethod(methodName, node.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), node.questionToken != null, item.fullClassName + "@" + methodName + "." + p.name.getText())), this.convertType(node.type, item.fullClassName + "@" + node.name.getText()), null, false, // private
        true, // static
        this.getJsDoc(node.name), this.prepareTypeParameters(node));
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
        var item = this.getHaxeTypeDeclarationByShort("interface", node.name.getText());
        if (this.typedefs.indexOf(item.fullClassName) >= 0)
            item.type = "typedef";
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => { }],
            [ts.SyntaxKind.HeritageClause, (x) => item.baseFullInterfaceNames = x.types.map(y => TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, y.getText()]))],
            [ts.SyntaxKind.PropertySignature, (x) => this.processPropertySignature(x, item)],
            [ts.SyntaxKind.MethodSignature, (x) => this.processMethodSignature(x, item)]
        ]));
        this.allHaxeTypes.push(item);
    }
    processClassDeclaration(node) {
        var item = this.getHaxeTypeDeclarationByShort("class", node.name.getText());
        item.docComment = this.getJsDoc(node.name);
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => { }],
            [ts.SyntaxKind.TypeParameter, (x) => this.processTypeParameter(x, item)],
            [ts.SyntaxKind.HeritageClause, (x) => this.processHeritageClauseForClass(x, item)],
            [ts.SyntaxKind.PropertyDeclaration, (x) => this.processPropertyDeclaration(x, item)],
            [ts.SyntaxKind.MethodDeclaration, (x) => this.processMethodDeclaration(x, item)],
            [ts.SyntaxKind.Constructor, (x) => this.processConstructor(x, item)]
        ]));
        this.allHaxeTypes.push(item);
    }
    processTypeParameter(node, dest) {
        dest.addTypeParameter(node.name.getText(), this.convertType(node.constraint, dest.fullClassName + "<" + node.name.getText()));
    }
    processEnumDeclaration(node) {
        var item = this.getHaxeTypeDeclarationByShort("enum", node.name.getText());
        item.docComment = this.getJsDoc(node.name);
        this.processChildren(node, new Map([
            [ts.SyntaxKind.ExportKeyword, (x) => { }],
            [ts.SyntaxKind.Identifier, (x) => { }],
            [ts.SyntaxKind.EnumMember, (x) => this.processEnumMember(x, item)],
        ]));
        this.allHaxeTypes.push(item);
    }
    processEnumMember(x, dest) {
        dest.addEnumMember(x.name.getText(), x.initializer != null ? " = " + x.initializer.getText() : "", this.getJsDoc(x.name));
    }
    processHeritageClauseForClass(x, dest) {
        switch (x.token) {
            case ts.SyntaxKind.ExtendsKeyword:
                dest.baseFullClassName = x.types.map(y => TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, y.getText()])).toString();
                break;
            case ts.SyntaxKind.ImplementsKeyword:
                dest.baseFullInterfaceNames = x.types.map(y => TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, y.getText()]));
                break;
        }
    }
    processPropertySignature(x, dest) {
        dest.addVar(this.createVar(x.name.getText(), x.type, null, this.getJsDoc(x.name), x.questionToken != null, dest.fullClassName + "@" + x.name.getText()));
    }
    processMethodSignature(x, dest) {
        var methodName = x.name.getText();
        dest.addMethod(methodName, x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@" + methodName + "." + p.name.getText())), this.convertType(x.type, dest.fullClassName + "@" + methodName), null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.name), this.prepareTypeParameters(x));
    }
    processPropertyDeclaration(x, dest) {
        var varName = x.name.getText();
        dest.addVar(this.createVar(varName, x.type, null, this.getJsDoc(x.name), x.questionToken != null, dest.fullClassName + "@" + varName));
    }
    processMethodDeclaration(x, dest) {
        var methodName = x.name.getText();
        dest.addMethod(methodName, x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@" + methodName + "." + p.name.getText())), this.convertType(x.type, dest.fullClassName + "@" + methodName), null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.name), this.prepareTypeParameters(x));
    }
    processConstructor(x, dest) {
        dest.addMethod("new", x.parameters.map(p => this.createVar(p.name.getText(), p.type, null, this.getJsDoc(p.name), p.questionToken != null, dest.fullClassName + "@new." + p.name.getText())), "Void", null, this.isFlag(x.modifiers, ts.NodeFlags.Private), this.isFlag(x.modifiers, ts.NodeFlags.Static), this.getJsDoc(x.getFirstToken()), this.prepareTypeParameters(x));
    }
    processChildren(node, map) {
        ts.forEachChild(node, x => {
            var f = map.get(x.kind);
            if (f) {
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
    logSubTree(node) {
        ts.forEachChild(node, x => {
            this.processNode(x, () => this.logSubTree(x));
        });
    }
    processNode(node, callb) {
        this.logger.log(this.indent + this.tokens[node.kind] + (node.name && node.name.getText() ? " | " + node.name.getText() : ""));
        this.indent += "    ";
        callb();
        this.indent = this.indent.substring(0, this.indent.length - 4);
    }
    //private report(node: ts.Node, message: string)
    //{
    //    let obj = this.sourceFile.getLineAndCharacterOfPosition(node.getStart());
    //    this.logger.log(`${this.sourceFile.fileName} (${obj.line + 1},${obj.character + 1}): ${message}`);
    //}
    addImports(moduleFilePath, ids) {
        for (let id of ids)
            this.imports.push(moduleFilePath.replace("/", ".") + "." + id);
    }
    createVar(name, type, defaultValue, jsDoc, isOptional, typeLocalePath) {
        return {
            haxeName: name,
            haxeType: this.convertType(type, typeLocalePath),
            haxeDefVal: defaultValue,
            jsDoc: jsDoc,
            isOptional: isOptional
        };
    }
    getJsDoc(node) {
        var symbol = this.typeChecker.getSymbolAtLocation(node);
        return symbol ? ts.displayPartsToString(symbol.getDocumentationComment()) : "";
    }
    isFlag(mods, f) {
        return mods && mods.flags && (mods.flags & f) !== 0;
    }
    getHaxeTypeDeclarationByShort(type, shortClassName) {
        return this.getHaxeTypeDeclarationByFull(type, TypePathTools_1.TypePathTools.makeFullClassPath([this.curPackage, shortClassName]));
    }
    getHaxeTypeDeclarationByFull(type, fullClassName) {
        var haxeType = this.allHaxeTypes.find(x => x.fullClassName == fullClassName);
        if (!haxeType) {
            haxeType = new HaxeTypeDeclaration_1.HaxeTypeDeclaration(type, fullClassName);
            if (type != "interface") {
                let relativePackage = fullClassName.startsWith(this.rootPackage + ".") ? fullClassName.substring(this.rootPackage.length + 1) : "";
                haxeType.addMeta('@:native("' + TypePathTools_1.TypePathTools.makeFullClassPath([this.nativeNamespace, relativePackage]) + '")');
            }
        }
        return haxeType;
    }
    getModuleClass(node) {
        let parts = this.curPackage.split(".");
        if (parts.length == 1 && parts[0] == "")
            parts[0] = "Root";
        else
            parts[parts.length - 1] = TypePathTools_1.TypePathTools.capitalize(parts[parts.length - 1]);
        let moduleName = parts.join(".");
        let curModuleClass = this.allHaxeTypes.find(x => x.fullClassName == moduleName);
        if (!curModuleClass) {
            curModuleClass = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("class", moduleName);
            var relativePackage = this.curPackage.startsWith(this.rootPackage + ".") ? this.curPackage.substring(this.rootPackage.length + 1) : "";
            curModuleClass.addMeta('@:native("' + TypePathTools_1.TypePathTools.makeFullClassPath([this.nativeNamespace, relativePackage]) + '")');
            this.allHaxeTypes.push(curModuleClass);
        }
        return curModuleClass;
    }
    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFunc` - return type of the function or variable
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    convertType(node, localePath) {
        if (!node)
            return "Dynamic";
        switch (node.kind) {
            case ts.SyntaxKind.FunctionType:
                {
                    let t = node;
                    let types = [];
                    if (t.parameters.length > 0)
                        for (var p of t.parameters)
                            types.push(this.convertType(p.type, p.name.getText()));
                    else
                        types.push("Void");
                    types.push(this.convertType(t.type, null));
                    return this.callTypeConvertor(types.join("->"), localePath);
                }
            case ts.SyntaxKind.ArrayType:
                {
                    let t = node;
                    let subType = t.elementType;
                    if (subType.kind == ts.SyntaxKind.ParenthesizedType)
                        subType = subType.type;
                    return this.callTypeConvertor("Array<" + this.convertType(subType, null) + ">", localePath);
                }
            case ts.SyntaxKind.UnionType:
                {
                    return this.callTypeConvertor(this.convertUnionType(node.types), localePath);
                }
            case ts.SyntaxKind.TypeLiteral:
                {
                    return this.processTypeLiteral(node);
                }
            case ts.SyntaxKind.TypeReference:
                {
                    let t = node;
                    if (t.typeArguments == null || t.typeArguments.length == 0) {
                        return this.callTypeConvertor(t.typeName.getText(), localePath);
                    }
                    else {
                        var s = this.callTypeConvertor(t.typeName.getText(), null);
                        var pp = t.typeArguments.map(x => this.convertType(x, null));
                        return this.callTypeConvertor(s + "<" + pp.join(", ") + ">", localePath);
                    }
                }
        }
        return this.callTypeConvertor(node.getText(), localePath);
    }
    callTypeConvertor(type, localePath) {
        return this.typeConvertor.convert(type, localePath, this.knownTypes, this.curPackage);
    }
    convertUnionType(types) {
        if (types.length == 1)
            return this.convertType(types[0], null);
        return "haxe.extern.EitherType<" + this.convertType(types[0], null) + ", " + this.convertUnionType(types.slice(1)) + ">";
    }
    processTypeLiteral(node) {
        if (node.members.length == 1 && node.members[0].kind == ts.SyntaxKind.IndexSignature) {
            let tt = node.members[0];
            if (tt.parameters.length == 1)
                return "Dynamic<" + this.convertType(tt.type, null) + ">";
        }
        var item = new HaxeTypeDeclaration_1.HaxeTypeDeclaration("");
        this.processChildren(node, new Map([
            [ts.SyntaxKind.PropertySignature, (x) => this.processPropertySignature(x, item)],
            [ts.SyntaxKind.MethodSignature, (x) => this.processMethodSignature(x, item)]
        ]));
        return item.toString();
    }
    prepareTypeParameters(node) {
        if (!node.typeParameters)
            return [];
        return node.typeParameters.map(t => ({ name: t.name.getText(), constraint: this.convertType(t.constraint, null) }));
    }
}
exports.DtsFileParser = DtsFileParser;
//# sourceMappingURL=DtsFileParser.js.map