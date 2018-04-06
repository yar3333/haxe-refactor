import * as ts from "typescript";
import { HaxeTypeDeclaration } from "./HaxeTypeDeclaration";
import { TypeMapper } from "./TypeMapper";

interface IParser
{
    curPackage : string;
    parseLiteralType(node:ts.TypeLiteralNode) : string;
    addNewEnumAsStringAbstract(localePath:string, values:Array<string>) : HaxeTypeDeclaration;
}

export class TypeConvertor
{
    constructor(private parser:IParser, private typeMapper:TypeMapper, private knownTypes:Array<string>)
    {
    }

    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFunc` - return type of the function or variable
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    convert(node:ts.Node, localePath:string) : string
    {
        if (!node) return "Dynamic";

        switch (node.kind)
        {
            case ts.SyntaxKind.FunctionType:
            {
                let t = <ts.FunctionTypeNode>node;
                
                let types = [];
                
                if (t.parameters.length > 0) for (var p of t.parameters) types.push(this.convert(p.type, p.name.getText()));
                else                         types.push("Void");
                
                types.push(this.convert(t.type, null));
                
                return this.mapType(types.join("->"), localePath);
            }

            case ts.SyntaxKind.ArrayType:
            {
                let t = <ts.ArrayTypeNode>node;
                let subType = t.elementType;
                if (subType.kind == ts.SyntaxKind.ParenthesizedType) subType = (<ts.ParenthesizedTypeNode>subType).type;
                return this.mapType("Array<" + this.convert(subType, null) + ">", localePath);
            }
            
            case ts.SyntaxKind.UnionType:
            {
                return this.mapType(this.convertUnionType((<ts.UnionTypeNode>node).types, localePath), localePath);
            }
            
            case ts.SyntaxKind.TypeLiteral:
            {
                return this.processTypeLiteral(<ts.TypeLiteralNode>node);
            }

            case ts.SyntaxKind.TypeReference:
            {
                let t = <ts.TypeReferenceNode>node;
                if (t.typeArguments == null || t.typeArguments.length == 0)
                {
                    return this.mapType(t.typeName.getText(), localePath);
                }
                else
                {
                    var s = this.mapType(t.typeName.getText(), null);
                    var pp = t.typeArguments.map(x => this.convert(x, null));
                    return this.mapType(s + "<" + pp.join(", ") + ">", localePath);
                }
            }
        }

        return this.mapType(node.getText(), localePath);
    }

    mapType(type:string, localePath:string)
    {
        return this.typeMapper.map(type, localePath, this.knownTypes, this.parser.curPackage);
    }

    private convertUnionType(types:Array<ts.TypeNode>, localePath:string) : string
    {
        //var hasNull = types.findIndex(x => this.isNull(x));
        //if (hasNull) types = types.filter(x => !this.isNull(x));
        types = types.filter(x => !this.isNull(x));

        var r: string; 

        if (types.length == 1)
        {
            r = this.convert(types[0], null);
        }
        else
        {
            var stringLiterals = types.filter(x => this.isStringLiteralType(x));

            if (stringLiterals.length > 0)
            {
                var otherTypes = types.filter(x => !this.isStringLiteralType(x));
                var newEnum = this.parser.addNewEnumAsStringAbstract(localePath, stringLiterals.map(x => (<ts.StringLiteral>x.getChildAt(0)).text));
                var mappedEnum = this.mapType(newEnum.fullClassName, null);
                r = otherTypes.length > 0 ? "haxe.extern.EitherType<" + mappedEnum + ", " + this.convertUnionType(otherTypes, null) + ">" : mappedEnum;
            }
            else
            {
                r = "haxe.extern.EitherType<" + this.convert(types[0], null) + ", " + this.convertUnionType(types.slice(1), null) + ">";
            }
        }

        //return hasNull ? "Null<" + r + ">" : r;
        return r;
    }

    private processTypeLiteral(node:ts.TypeLiteralNode) : string
    {
        if (node.members.length == 1 && node.members[0].kind == ts.SyntaxKind.IndexSignature)
        {
            let tt = <ts.IndexSignatureDeclaration>node.members[0];
            if (tt.parameters.length == 1) return "Dynamic<" + this.convert(tt.type, null) + ">";
        }
    
       return this.parser.parseLiteralType(node);
    }

    private prepareTypeParameters(node:{ typeParameters?:ts.NodeArray<ts.TypeParameterDeclaration> }) : Array<{ name:string, constraint:string }>
    {
        if (!node.typeParameters) return [];
        return node.typeParameters.map(t => ({ name:t.name.getText(), constraint:this.convert(t.constraint, null) }))
    }

    private isStringLiteralType(x:ts.TypeNode)
    {
        return x.kind == ts.SyntaxKind.LastTypeNode && x.getChildCount() == 1 && x.getChildAt(0).kind == ts.SyntaxKind.StringLiteral;
    }

    private isNull(x:ts.TypeNode)
    {
        return x.kind == ts.SyntaxKind.NullKeyword;
    }
}