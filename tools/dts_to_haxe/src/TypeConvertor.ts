import * as ts from "typescript";
import { HaxeTypeDeclaration } from "./HaxeTypeDeclaration";
import { TypeMapper } from "./TypeMapper";

export class TypeConvertor
{
    constructor(private parser:{ curPackage:string; parseLiteralType(node:ts.TypeLiteralNode):string; }, private typeMapper:TypeMapper, private knownTypes:Array<string>)
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
                
                return this.callTypeConvertor(types.join("->"), localePath);
            }

            case ts.SyntaxKind.ArrayType:
            {
                let t = <ts.ArrayTypeNode>node;
                let subType = t.elementType;
                if (subType.kind == ts.SyntaxKind.ParenthesizedType) subType = (<ts.ParenthesizedTypeNode>subType).type;
                return this.callTypeConvertor("Array<" + this.convert(subType, null) + ">", localePath);
            }
            
            case ts.SyntaxKind.UnionType:
            {
                return this.callTypeConvertor(this.convertUnionType((<ts.UnionTypeNode>node).types), localePath);
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
                    return this.callTypeConvertor(t.typeName.getText(), localePath);
                }
                else
                {
                    var s = this.callTypeConvertor(t.typeName.getText(), null);
                    var pp = t.typeArguments.map(x => this.convert(x, null));
                    return this.callTypeConvertor(s + "<" + pp.join(", ") + ">", localePath);
                }
            }
        }

        return this.callTypeConvertor(node.getText(), localePath);
    }

    private callTypeConvertor(type:string, localePath:string)
    {
        return this.typeMapper.map(type, localePath, this.knownTypes, this.parser.curPackage);
    }

    private convertUnionType(types:Array<ts.TypeNode>) : string
    {
        if (types.length == 1) return this.convert(types[0], null);
        return "haxe.extern.EitherType<" + this.convert(types[0], null)+", " +  this.convertUnionType(types.slice(1)) + ">";
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
}