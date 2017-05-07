export interface HaxeVar
{
	haxeName : string;
	haxeType : string;
	haxeDefVal : string;
	jsDoc : string;
}

export interface HaxeVarGetter
{
	haxeName : string;
	haxeType : string;
	haxeBody : string;
}

export class HaxeTypeDeclaration
{
	public type : "class" | "interface" | "enum" | "";

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
	
	constructor(type:"class"|"interface"|"enum"|"", fullClassName="")
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
	
	public addVar(v:HaxeVar, isPrivate:boolean=false, isStatic=false, isReadOnlyProperty=false) : void
	{
		var s = this.jsDocToString(v.jsDoc);
		s += (isPrivate ? "private " : "");
		s += (isStatic ? "static " : "");
		s += "var " + v.haxeName + (isReadOnlyProperty ? "(default, null)" : "") + " : " + v.haxeType
			  + (isStatic && v.haxeDefVal != null ? " = " + v.haxeDefVal : "")
			  + ";";
		this.vars.push(s);
 	}
	
	public addVarGetter(v:HaxeVarGetter, isPrivate = false, isStatic = false, isInline = false) : void
	{
		var s = "\n\t"
		      + (isPrivate ? "private " : "")
			  + (isStatic ? "static " : "")
			  + "var " + v.haxeName + "(get_" + v.haxeName + ", null)" + " : " + v.haxeType
			  + ";\n";
		
		s += (isInline ? "\tinline " : "\t")
		   + "function get_" + v.haxeName + "() : " + v.haxeType + "\n"
		   + "\t{\n"
		   + this.indent(v.haxeBody.trim(), "\t\t") + "\n"
		   + "\t}";
		
		this.vars.push(s);
	}
	
	public addMethod(name:string, vars:Array<HaxeVar>, retType:string, body:string, isPrivate:boolean, isStatic:boolean, jsDoc?:string) : void
	{
		var header = 
			    this.jsDocToString(jsDoc)
			  + (isPrivate ? 'private ' : '')
			  + (isStatic ? 'static  ' : '')
			  + 'function ' + name + '('
			  + vars.map((v:HaxeVar) => v.haxeName + ":" + v.haxeType + (v.haxeDefVal != null ? '=' + v.haxeDefVal : '')).join(', ')
			  + ') : ' + retType;
		var s = header;
		if (body !== null)
		{
			s += '\n';
			s += '\t{\n';
			s += this.indent(body.trim(), '\t\t') + '\n';
			s += '\t}';
		}
		else
		{
			s += ";";
		}
		
		this.methods.push(s);
 	}
	
	public addEnumMember(name:string, value:string, jsDoc:string) : void
	{
		var s = this.jsDocToString(jsDoc);
		s += name + (value != null ? value : "") + ";";
		this.enumMembers.push(s);
 	}

	public addCustom(code:string) : void
	{
		this.customs.push(code);
	}
	
	public toString() : string
	{
		var clas = this.splitFullClassName(this.fullClassName);
		
		var s = "";

		
		if (this.type != "")
		{
			if (clas.packageName) s += "package " + clas.packageName + ";\n\n";
			
			s += this.imports.join("\n") + (this.imports.length > 0 ? "\n\n" : "");
			
			s += this.jsDocToString(this.docComment);

			s += this.metas.map(m => m + "\n").join("\n");
			s += "extern " + this.type + " " + clas.className;

			switch (this.type)
			{
				case "class":
					s += (this.baseFullClassName ? " extends " + this.getShortClassName(clas.packageName, this.baseFullClassName) : "") + "\n";
					if (this.baseFullInterfaceNames.length > 0) s += "\timplements " + this.baseFullInterfaceNames.map(x => this.getShortClassName(clas.packageName, x)).join(", ") + "\n";
					break;

				case "interface":
					if (this.baseFullInterfaceNames.length == 1) s += " extends " + this.baseFullInterfaceNames.map(x => this.getShortClassName(clas.packageName, x)).join(", ");
					else if (this.baseFullInterfaceNames.length > 1) s += "\n\t" + this.baseFullInterfaceNames.map(x => "extends " + this.getShortClassName(clas.packageName, x)).join("\n\t");
					s += "\n"
					break;

				case "enum":
					s += "\n";
					break;
			}
		}
		s += "{\n";
		s += (this.vars.length > 0 ? "\t" + (this.vars.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n\n" : "");
		s += (this.methods.length > 0 ? "\t" + (this.methods.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
		s += (this.customs.length > 0 ? "\t" + (this.customs.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
		s += (this.enumMembers.length > 0 ? "\t" + (this.enumMembers.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");

		if (s.endsWith("\n\n")) s = s.substring(0, s.length-1);

		s += "}";

		if (this.type == "") s = s.replace(/[ \t\n]+/g," ");

		return s;
	}
	
	private indent(text:string, ind = "\t") : string
    {
        if (text == '') return '';
		return ind + text.replace("\n", "\n" + ind);
    }
	
	private splitFullClassName(fullClassName:string) : { packageName:string, className:string }
	{
		var packageName = '';
		var className = fullClassName;
		
		if (fullClassName.lastIndexOf('.') != -1)
		{
			packageName = fullClassName.substr(0, fullClassName.lastIndexOf('.'));
			className = fullClassName.substr(fullClassName.lastIndexOf('.') + 1);
		}
		
		return { packageName:packageName, className:className };
	}

	private jsDocToString(jsDoc:string) : string
	{
		if (jsDoc === null || jsDoc === "") return "";
		return "/"+"**\n * " + jsDoc.split("\r\n").join("\n").split("\n").join("\n * ") + "\n *" + "/\n";
	}

	private getShortClassName(pack:string, fullClassName:string) : string
	{
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