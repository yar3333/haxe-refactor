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
                    return this.callTypeConvertor(types.join("->"), localePath);
                }
            case ts.SyntaxKind.ArrayType:
                {
                    let t = node;
                    let subType = t.elementType;
                    if (subType.kind == ts.SyntaxKind.ParenthesizedType)
                        subType = subType.type;
                    return this.callTypeConvertor("Array<" + this.convert(subType, null) + ">", localePath);
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
                        var pp = t.typeArguments.map(x => this.convert(x, null));
                        return this.callTypeConvertor(s + "<" + pp.join(", ") + ">", localePath);
                    }
                }
        }
        return this.callTypeConvertor(node.getText(), localePath);
    }
    callTypeConvertor(type, localePath) {
        return this.typeMapper.map(type, localePath, this.knownTypes, this.parser.curPackage);
    }
    convertUnionType(types) {
        if (types.length == 1)
            return this.convert(types[0], null);
        return "haxe.extern.EitherType<" + this.convert(types[0], null) + ", " + this.convertUnionType(types.slice(1)) + ">";
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
}
exports.TypeConvertor = TypeConvertor;
//# sourceMappingURL=TypeConvertor.js.map