"use strict";
const TypePathTools_1 = require("./TypePathTools");
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
        this.typeParameters = new Array();
        this.type = type;
        this.fullClassName = fullClassName;
    }
    addImport(packageName, append = true) {
        var s = "import " + packageName + ";";
        if (append)
            this.imports.push(s);
        else
            this.imports.unshift(s);
    }
    addImports(packageNames, append = true) {
        var arr = packageNames.slice(0);
        if (!append)
            arr.reverse();
        for (let p of arr)
            this.addImport(p);
    }
    addMeta(meta) {
        this.metas.push(meta);
    }
    addVar(v, isPrivate = false, isStatic = false, isReadOnlyProperty = false) {
        if (HaxeTypeDeclaration.reserved.indexOf(v.haxeName) >= 0) {
            var originalName = v.haxeName;
            v.haxeName += "_";
            this.vars.push(this.varGetterToString(v, "return (cast this)[cast '" + originalName + "'];", "null", isPrivate, isStatic, true));
            v.haxeName = originalName;
        }
        else {
            var s = this.jsDocToString(v.jsDoc);
            if (v.isOptional)
                s += "@:optional ";
            if (isPrivate)
                s += "private ";
            if (isStatic)
                s += "static ";
            s += "var " + v.haxeName + (isReadOnlyProperty ? "(default, null)" : "") + " : " + this.trimTypePath(v.haxeType)
                + (isStatic && v.haxeDefVal != null ? " = " + v.haxeDefVal : "")
                + ";";
            this.vars.push(s);
        }
    }
    addVarGetter(v, getter, setter, isPrivate = false, isStatic = false, isInline = false) {
        this.vars.push(this.varGetterToString(v, getter, setter, isPrivate, isStatic, isInline));
    }
    varGetterToString(v, getter, setter, isPrivate = false, isStatic = false, isInline = false) {
        var isGetterSpecial = getter == "null" || getter == "never";
        var isSetterSpecial = setter == "null" || setter == "never";
        var s = this.jsDocToString(v.jsDoc);
        if (v.isOptional)
            s += "@:optional ";
        if (isPrivate)
            s += "private ";
        if (isStatic)
            s += "static ";
        s += "var " + v.haxeName + "(" + (isGetterSpecial ? getter : "get") + ", " + (isSetterSpecial ? setter : "set") + ")" + " : " + this.trimTypePath(v.haxeType) + ";";
        if (!isGetterSpecial) {
            s += "\n";
            if (isInline)
                s += "inline ";
            s += "function get_" + v.haxeName + "() : " + this.trimTypePath(v.haxeType) + (getter.indexOf("\n") >= 0 ? "\n" : " ") + getter;
        }
        if (!isSetterSpecial) {
            s += "\n";
            if (isInline)
                s += "inline ";
            s += "function set_" + v.haxeName + "() : " + this.trimTypePath(v.haxeType) + (setter.indexOf("\n") >= 0 ? "\n" : " ") + setter;
        }
        return s;
    }
    addMethod(name, vars, retType, body, isPrivate, isStatic, jsDoc, typeParameters) {
        var header = this.jsDocToString(jsDoc)
            + (isPrivate ? 'private ' : '')
            + (isStatic ? 'static ' : '')
            + 'function ' + name
            + (typeParameters && typeParameters.length > 0 ? "<" + typeParameters.map(t => t.name + (t.constraint ? ":" + t.constraint : "")).join(", ") + ">" : "")
            + '('
            + vars.map(v => this.parameterToString(v)).join(', ')
            + ') : ' + this.trimTypePath(retType);
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
    parameterToString(v) {
        var s = "";
        if (v.isOptional)
            s += "?";
        s += HaxeTypeDeclaration.reserved.indexOf(v.haxeName) >= 0 ? v.haxeName + "_" : v.haxeName;
        s += ":" + this.trimTypePath(v.haxeType);
        if (v.haxeDefVal !== null && v.haxeDefVal !== undefined)
            s += '=' + v.haxeDefVal;
        return s;
    }
    addEnumMember(name, value, jsDoc) {
        var s = this.jsDocToString(jsDoc);
        s += name + (value != null ? value : "") + ";";
        this.enumMembers.push(s);
    }
    addCustom(code) {
        this.customs.push(code);
    }
    addTypeParameter(name, constraint) {
        this.typeParameters.push({ name: name, constraint: this.trimTypePath(constraint) });
    }
    toString() {
        var packAndClass = TypePathTools_1.TypePathTools.splitFullClassName(TypePathTools_1.TypePathTools.normalizeFullClassName(this.fullClassName));
        var s = "";
        if (this.type != "") {
            if (packAndClass.packageName)
                s += "package " + packAndClass.packageName + ";\n\n";
            s += this.imports.join("\n") + (this.imports.length > 0 ? "\n\n" : "");
            s += this.jsDocToString(this.docComment);
            s += this.metas.map(m => m + "\n").join("\n");
            s += (this.type != "typedef" ? "extern " : "") + this.type + " " + packAndClass.className;
            if (this.typeParameters.length > 0) {
                s += "<" + this.typeParameters.map(x => x.name + ":" + x.constraint).join(", ") + ">";
            }
            if (this.type == "typedef")
                s += " =\n{";
            switch (this.type) {
                case "class":
                    s += (this.baseFullClassName ? " extends " + this.trimTypePath(this.baseFullClassName) : "") + "\n";
                    if (this.baseFullInterfaceNames.length > 0)
                        s += "\timplements " + this.baseFullInterfaceNames.map(x => this.trimTypePath(x)).join(", ") + "\n";
                    break;
                case "interface":
                    if (this.baseFullInterfaceNames.length == 1)
                        s += " extends " + this.baseFullInterfaceNames.map(x => this.trimTypePath(x)).join(", ");
                    else if (this.baseFullInterfaceNames.length > 1)
                        s += "\n\t" + this.baseFullInterfaceNames.map(x => "extends " + this.trimTypePath(x)).join("\n\t");
                    s += "\n";
                    break;
                case "typedef":
                    s += this.baseFullInterfaceNames.map(x => ">" + this.trimTypePath(x) + ",").join(" ") + "\n";
                    break;
                case "enum":
                    s += "\n";
                    break;
            }
        }
        if (this.type != "typedef")
            s += "{\n";
        s += (this.vars.length > 0 ? "\t" + (this.vars.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n\n" : "");
        s += (this.methods.length > 0 ? "\t" + (this.methods.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
        s += (this.customs.length > 0 ? "\t" + (this.customs.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
        s += (this.enumMembers.length > 0 ? "\t" + (this.enumMembers.map(x => x.split("\n").join("\n\t"))).join("\n\t") + "\n" : "");
        if (s.endsWith("\n\n"))
            s = s.substring(0, s.length - 1);
        s += "}";
        if (this.type == "")
            s = s.replace(/[ \t\n]+/g, " ");
        return s;
    }
    indent(text, ind = "\t") {
        if (text == '')
            return '';
        return ind + text.replace("\n", "\n" + ind);
    }
    jsDocToString(jsDoc) {
        if (jsDoc === null || jsDoc === "")
            return "";
        return "/" + "**\n * " + jsDoc.split("\r\n").join("\n").split("\n").join("\n * ") + "\n *" + "/\n";
    }
    trimTypePath(fullClassName) {
        var pack = TypePathTools_1.TypePathTools.splitFullClassName(TypePathTools_1.TypePathTools.normalizeFullClassName(this.fullClassName)).packageName;
        if (pack === null || pack === "")
            return fullClassName;
        var partsA = pack.split(".");
        var partsB = fullClassName.split(".");
        if (partsB.length == 1 || partsB.length >= partsA.length)
            for (var i = 0; i < partsB.length - 1; i++) {
                if (partsA[i] !== partsB[i])
                    return fullClassName;
            }
        return partsB[partsB.length - 1];
    }
}
HaxeTypeDeclaration.reserved = ["dynamic", "catch", "throw"];
exports.HaxeTypeDeclaration = HaxeTypeDeclaration;
//# sourceMappingURL=HaxeTypeDeclaration.js.map