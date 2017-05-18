"use strict";
const ts = require("typescript");
class TypeConvertor {
    constructor(parser, typeMapper, knownTypes) {
        this.parser = parser;
        this.typeMapper = typeMapper;
        this.knownTypes = knownTypes;
    }
    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFunc` - return type of the function or variable
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    convert(node, localePath) {
        if (!node)
            return "Dynamic";
        switch (node.kind) {
            case ts.SyntaxKind.FunctionType:
                {
                    let t = node;
                    let types = [];
                    if (t.parameters.length > 0)
                        for (var p of t.parameters)
                            types.push(this.convert(p.type, p.name.getText()));
                    else
                        types.push("Void");
                    types.push(this.convert(t.type, null));
                    return this.mapType(types.join("->"), localePath);
                }
            case ts.SyntaxKind.ArrayType:
                {
                    let t = node;
                    let subType = t.elementType;
                    if (subType.kind == ts.SyntaxKind.ParenthesizedType)
                        subType = subType.type;
                    return this.mapType("Array<" + this.convert(subType, null) + ">", localePath);
                }
            case ts.SyntaxKind.UnionType:
                {
                    return this.mapType(this.convertUnionType(node.types, localePath), localePath);
                }
            case ts.SyntaxKind.TypeLiteral:
                {
                    return this.processTypeLiteral(node);
                }
            case ts.SyntaxKind.TypeReference:
                {
                    let t = node;
                    if (t.typeArguments == null || t.typeArguments.length == 0) {
                        return this.mapType(t.typeName.getText(), localePath);
                    }
                    else {
                        var s = this.mapType(t.typeName.getText(), null);
                        var pp = t.typeArguments.map(x => this.convert(x, null));
                        return this.mapType(s + "<" + pp.join(", ") + ">", localePath);
                    }
                }
        }
        return this.mapType(node.getText(), localePath);
    }
    mapType(type, localePath) {
        return this.typeMapper.map(type, localePath, this.knownTypes, this.parser.curPackage);
    }
    convertUnionType(types, localePath) {
        if (types.length == 1)
            return this.convert(types[0], null);
        var stringLiterals = types.filter(x => this.isStringLiteralType(x));
        var otherTypes = types.filter(x => !this.isStringLiteralType(x));
        if (stringLiterals.length > 0) {
            var newEnum = this.parser.addNewEnumAsStringAbstract(localePath, stringLiterals.map(x => x.getChildAt(0).text));
            var mappedEnum = this.mapType(newEnum.fullClassName, null);
            return otherTypes.length > 0 ? "haxe.extern.EitherType<" + mappedEnum + ", " + this.convertUnionType(otherTypes, null) + ">" : mappedEnum;
        }
        else {
            return "haxe.extern.EitherType<" + this.convert(types[0], null) + ", " + this.convertUnionType(types.slice(1), null) + ">";
        }
    }
    processTypeLiteral(node) {
        if (node.members.length == 1 && node.members[0].kind == ts.SyntaxKind.IndexSignature) {
            let tt = node.members[0];
            if (tt.parameters.length == 1)
                return "Dynamic<" + this.convert(tt.type, null) + ">";
        }
        return this.parser.parseLiteralType(node);
    }
    prepareTypeParameters(node) {
        if (!node.typeParameters)
            return [];
        return node.typeParameters.map(t => ({ name: t.name.getText(), constraint: this.convert(t.constraint, null) }));
    }
    isStringLiteralType(x) {
        return x.kind == ts.SyntaxKind.LastTypeNode && x.getChildCount() == 1 && x.getChildAt(0).kind == ts.SyntaxKind.StringLiteral;
    }
}
exports.TypeConvertor = TypeConvertor;
//# sourceMappingURL=TypeConvertor.js.map