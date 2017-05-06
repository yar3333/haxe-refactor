"use strict";
class HaxeTypeDeclaration {
    constructor(type, fullClassName = "") {
        this.docComment = "";
        this.fullClassName = "";
        this.baseFullClassName = "";
        this.baseFullInterfaceNames = new Array();
        this.imports = new Array();
        this.metas = new Array();
        this.vars = new Array();
        this.methods = new Array();
        this.customs = new Array();
        this.enumMembers = new Array();
        this.type = type;
        this.fullClassName = fullClassName;
    }
    addImport(packageName) {
        this.imports.push("import " + packageName + ";");
    }
    addMeta(meta) {
        this.metas.push(meta);
    }
    addVar(v, isPrivate = false, isStatic = false, isReadOnlyProperty = false) {
        var s = this.jsDocToString(v.jsDoc);
        s += (isPrivate ? "" : "public ");
        s += (isStatic ? "static " : "");
        s += "var " + v.haxeName + (isReadOnlyProperty ? "(default, null)" : "") + " : " + v.haxeType
            + (isStatic && v.haxeDefVal != null ? " = " + v.haxeDefVal : "")
            + ";";
        this.vars.push(s);
    }
    addVarGetter(v, isPrivate = false, isStatic = false, isInline = false) {
        var s = "\n\t"
            + (isPrivate ? "" : "public ")
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
    addMethod(name, vars, retType, body, isPrivate, isStatic, jsDoc) {
        var header = this.jsDocToString(jsDoc)
            + (isPrivate ? '' : 'public ')
            + (isStatic ? 'static  ' : '')
            + 'function ' + name + '('
            + vars.map((v) => v.haxeName + ":" + v.haxeType + (v.haxeDefVal != null ? '=' + v.haxeDefVal : '')).join(', ')
            + ') : ' + retType;
        var s = header;
        if (body !== null) {
            s += '\n';
            s += '\t{\n';
            s += this.indent(body.trim(), '\t\t') + '\n';
            s += '\t}';
        }
        else {
            s += ";";
        }
        this.methods.push(s);
    }
    addEnumMember(name, value, jsDoc) {
        var s = this.jsDocToString(jsDoc);
        s += name + (value != null ? value : "") + ";";
        this.enumMembers.push(s);
    }
    addCustom(code) {
        this.customs.push(code);
    }
    toString() {
        var clas = this.splitFullClassName(this.fullClassName);
        var s = "";
        if (clas.packageName)
            s += "package " + clas.packageName + ";\n\n";
        s += this.imports.join("\n") + (this.imports.length > 0 ? "\n\n" : "");
        s += this.jsDocToString(this.docComment);
        s += this.metas.map(m => m + "\n").join("\n");
        s += this.type + " " + clas.className;
        switch (this.type) {
            case "class":
                s += (this.baseFullClassName ? " extends " + this.baseFullClassName : "") + "\n";
                if (this.baseFullInterfaceNames.length > 0)
                    s += "\timplements " + this.baseFullInterfaceNames.join(", ") + "\n";
                break;
            case "interface":
                if (this.baseFullInterfaceNames.length > 0)
                    s += " extends " + this.baseFullInterfaceNames.join(", ");
                s += "\n";
                break;
            case "enum":
                s += "\n";
                break;
        }
        s += "{\n";
        s += (this.vars.length > 0 ? "\t" + (this.vars.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n\n" : "");
        s += (this.methods.length > 0 ? "\t" + (this.methods.map(x => x.split("\n").join("\n\t"))).join("\n\n\t") + "\n" : "");
        s += (this.customs.length > 0 ? "\t" + (this.customs.map(x => x.split("\n").join("\n\t"))).join("\n\n\t") + "\n" : "");
        s += (this.enumMembers.length > 0 ? "\t" + (this.enumMembers.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
        if (s.endsWith("\n\n"))
            s = s.substring(0, s.length - 1);
        s += "}";
        return s;
    }
    indent(text, ind = "\t") {
        if (text == '')
            return '';
        return ind + text.replace("\n", "\n" + ind);
    }
    splitFullClassName(fullClassName) {
        var packageName = '';
        var className = fullClassName;
        if (fullClassName.lastIndexOf('.') != -1) {
            packageName = fullClassName.substr(0, fullClassName.lastIndexOf('.'));
            className = fullClassName.substr(fullClassName.lastIndexOf('.') + 1);
        }
        return { packageName: packageName, className: className };
    }
    jsDocToString(jsDoc) {
        if (jsDoc === null || jsDoc === "")
            return "";
        return "/" + "**\n * " + jsDoc.split("\r\n").join("\n").split("\n").join("\n * ") + "\n *" + "/\n";
    }
}
exports.HaxeTypeDeclaration = HaxeTypeDeclaration;
