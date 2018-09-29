import { TypePathTools } from "./TypePathTools";

export interface HaxeVar
{
	haxeName : string;
	haxeType : string;
	haxeDefVal : string;
	jsDoc : string;
	isOptional : boolean;
}

export class HaxeTypeDeclaration
{
	static reserved = [ "dynamic", "catch", "throw", "continue" ];

	public type : "class" | "interface" | "enum" | "typedef" | "abstract" | "";

	docComment = "";
	fullClassName = "";
	baseFullClassName = "";
	baseFullInterfaceNames = new Array<string>();
	
	private imports = new Array<string>();
	private metas = new Array<string>();
	private vars = new Array<string>();
	private methods = new Array<string>();
	private customs = new Array<string>();
	private enumMembers = new Array<string>();
	private typeParameters = new Array<{ name:string, constraint:string }>();
	private aliasTypeText: string;
	
	constructor(type:"class"|"interface"|"enum"|"typedef"|"abstract"|"", fullClassName="")
	{
		this.type = type;
		this.fullClassName = fullClassName;
	}
	
	public addImport(packageName:string, append=true) : void
	{
		var s = "import " + packageName + ";";
		if (append) this.imports.push(s);
		else        this.imports.unshift(s);
	}
	
	public addImports(packageNames:Array<string>, append=true) : void
	{
		var arr = packageNames.slice(0);
		if (!append) arr.reverse();
		for (let p of arr) this.addImport(p);
	}
	
	public addMeta(meta:string) : void
	{
		this.metas.push(meta);
	}
	
	public addVar(v:HaxeVar, isPrivate=false, isStatic=false, isReadOnlyProperty=false) : void
	{
		if (HaxeTypeDeclaration.reserved.indexOf(v.haxeName) >= 0)
		{
			var originalName = v.haxeName;
			v.haxeName += "_";
			this.vars.push(this.varGetterToString(v, "return (cast this)[cast '" + originalName + "'];", "null", isPrivate, isStatic, true));
			v.haxeName = originalName;
		}
		else
		{
			var s = this.jsDocToString(v.jsDoc);
			if (v.isOptional) s += "@:optional ";
			if (isPrivate) s += "private ";
			if (isStatic) s += "static ";
			s += "var " + v.haxeName + (isReadOnlyProperty ? "(default, null)" : "")
				+ (this.type != "abstract" ? " : " + this.trimTypePath(v.haxeType) : "")
				+ (this.type == "abstract" || isStatic && v.haxeDefVal != null ? " = " + v.haxeDefVal : "")
				+ ";";
			this.vars.push(s);
		}
	 }
	
	public addVarGetter(v:HaxeVar, getter:string, setter:string, isPrivate=false, isStatic=false, isInline=false) : void
	{
		this.vars.push(this.varGetterToString(v, getter, setter, isPrivate, isStatic, isInline));
	}

	private varGetterToString(v:HaxeVar, getter:string, setter:string, isPrivate=false, isStatic=false, isInline=false) : string
	{
		var isGetterSpecial = getter == "null" || getter == "never";
		var isSetterSpecial = setter == "null" || setter == "never";

		var s = this.jsDocToString(v.jsDoc);
		if (v.isOptional) s += "@:optional ";
		if (isPrivate) s += "private ";
		if (isStatic) s += "static ";
		s += "var " + v.haxeName + "(" + (isGetterSpecial ? getter : "get") + ", " + (isSetterSpecial ? setter : "set") + ")" + " : " + this.trimTypePath(v.haxeType) + ";";
		
		if (!isGetterSpecial)
		{
			s += "\n";
			if (isInline) s += "inline ";
			s += "function get_" + v.haxeName + "() : " + this.trimTypePath(v.haxeType) + (getter.indexOf("\n") >= 0 ? "\n" : " ") + getter;
		}
		
		if (!isSetterSpecial)
		{
			s += "\n";
			if (isInline) s += "inline ";
			s += "function set_" + v.haxeName + "() : " + this.trimTypePath(v.haxeType) + (setter.indexOf("\n") >= 0 ? "\n" : " ") + setter;
		}
		
		return s;
	}
	
	public addMethod(name:string, vars:Array<HaxeVar>, retType:string, body:string, isPrivate:boolean, isStatic:boolean, jsDoc:string, typeParameters:Array<{ name:string, constraint:string }>) : void
	{
		var realBody = body !== null ? '\n\t{\n' + this.indent(body.trim(), '\t\t') + '\n\t}' : ';';

		var isMapReserved = HaxeTypeDeclaration.reserved.indexOf(name) >= 0 && body === null;
		var prefix = "";
		
		if (isMapReserved) {
			realBody = " return (cast this)[cast '" + name + "'](" + vars.map(x => x.haxeName).join(", ") + ");";
			name = name + "_";
			prefix = "inline ";
		}
		
		var header = 
				this.jsDocToString(jsDoc)
			  + prefix
			  + (isPrivate ? 'private ' : '')
			  + (isStatic ? 'static ' : '')
			  + 'function ' + name
			  + (typeParameters && typeParameters.length > 0 ? "<" + typeParameters.map(t => t.name + (t.constraint ? ":" + t.constraint : "")).join(", ") + ">" : "") 
			  + '('
			  + vars.map(v => this.parameterToString(v)).join(', ')
			  + ') : ' + this.trimTypePath(retType);
		
		var s = header + realBody;
		
		this.methods.push(s);
 	}
	
	private parameterToString(v:HaxeVar) : string
	{
		var s = "";
		if (v.isOptional) s += "?";
		s += HaxeTypeDeclaration.reserved.indexOf(v.haxeName) >= 0 ? v.haxeName+"_" : v.haxeName;
		s += ":" + this.trimTypePath(v.haxeType);
		if (v.haxeDefVal !== null && v.haxeDefVal !== undefined) s += '=' + v.haxeDefVal;
		return s;
	}
	
	public addEnumMember(name:string, value:string, jsDoc:string) : void
	{
		var s = this.jsDocToString(jsDoc);
		s += "var " + name + (value != null ? value : "") + ";";
		this.enumMembers.push(s);
 	}

	public addCustom(code:string) : void
	{
		this.customs.push(code);
	}

	public addTypeParameter(name:string, constraint:string)
	{
		this.typeParameters.push({ name:name, constraint:this.trimTypePath(constraint) });
	}

	public setAliasTypeText(typeText:string)
	{
		this.aliasTypeText = typeText;
	}
	
	public toString() : string
	{
		var packAndClass = TypePathTools.splitFullClassName(TypePathTools.normalizeFullClassName(this.fullClassName));
		
		var s = "";
		
		if (this.type != "")
		{
			if (packAndClass.packageName) s += "package " + packAndClass.packageName + ";\n\n";
			
			s += this.imports.join("\n") + (this.imports.length > 0 ? "\n\n" : "");
			
			s += this.jsDocToString(this.docComment);

			s += this.metas.map(m => m + "\n").join("\n");
			s += (["typedef", "abstract"].indexOf(this.type) < 0 ? "extern " : (this.type == "abstract" ? "@:enum " : "")) + this.type + " " + packAndClass.className;

			if (this.typeParameters.length > 0)
			{
				s += "<" + this.typeParameters.map(x => x.name + ":" + x.constraint).join(", ") + ">";
			}

			switch (this.type)
			{
				case "class":
					if (this.baseFullClassName) s += " extends " + this.trimTypePath(this.baseFullClassName);
					if (this.baseFullInterfaceNames.length > 0) s += this.baseFullInterfaceNames.map(x => "\n\timplements " + this.trimTypePath(x)).join("");
					s += "\n{\n";
					break;

				case "interface":
					if      (this.baseFullInterfaceNames.length == 1) s += " extends " + this.trimTypePath(this.baseFullInterfaceNames[0]);
					else if (this.baseFullInterfaceNames.length > 1 ) s += this.baseFullInterfaceNames.map(x => "\n\textends " + this.trimTypePath(x)).join("");
					s += "\n{\n";
					break;

				case "typedef":
					s += !this.aliasTypeText ? " =\n{" + this.baseFullInterfaceNames.map(x => ">" + this.trimTypePath(x) + ",").join(" ") + "\n" : " = " + this.aliasTypeText + ";";
					break;

				case "enum":
					s += "\n{\n";
					break;

				case "abstract":
					s += "(" + this.trimTypePath(this.baseFullClassName) + ")\n{\n";
					break;
			}
		}
		else
		{
			s += "{";
		}

		if (this.type != "typedef" || !this.aliasTypeText) {
			s += (this.vars.length > 0 ? "\t" + (this.vars.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n\n" : "");
			s += (this.methods.length > 0 ? "\t" + (this.methods.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
			s += (this.customs.length > 0 ? "\t" + (this.customs.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
			s += (this.enumMembers.length > 0 ? "\t" + (this.enumMembers.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
	
			if (s.endsWith("\n\n")) s = s.substring(0, s.length - 1);
			
			s += "}";
		}

		if (this.type == "") s = s.replace(/[ \t\n]+/g," ");

		return s;
	}
	
	private indent(text:string, ind = "\t") : string
    {
        if (text == '') return '';
		return ind + text.replace("\n", "\n" + ind);
    }
	
	private jsDocToString(jsDoc:string) : string
	{
		if (jsDoc === null || jsDoc === "") return "";
		return "/"+"**\n * " + jsDoc.split("\r\n").join("\n").split("\n").join("\n * ") + "\n *" + "/\n";
	}

	private trimTypePath(fullClassName:string) : string
	{
		var pack = TypePathTools.splitFullClassName(TypePathTools.normalizeFullClassName(this.fullClassName)).packageName;
		if (pack === null || pack === "") return fullClassName;
		
		var partsA = pack.split(".");
		var partsB = fullClassName.split(".");
		
		if (partsB.length == 1 || partsB.length >= partsA.length)

		for (var i = 0; i < partsB.length - 1; i++)
		{
			if (partsA[i] !== partsB[i]) return fullClassName;
		}

		return partsB[partsB.length - 1];
	}
}