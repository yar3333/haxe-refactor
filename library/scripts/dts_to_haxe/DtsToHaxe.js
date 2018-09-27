/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const fs = require("fs");
const path = require("path");
const ts = require("typescript");
const DtsFileParser_1 = require("./DtsFileParser");
const CmdOptions_1 = require("./CmdOptions");
const Logger_1 = require("./Logger");
const FsTools = require("./FsTools");
const TypeMapper_1 = require("./TypeMapper");
const TypePathTools_1 = require("./TypePathTools");
const DtsFilePossibleTypesFinder_1 = require("./DtsFilePossibleTypesFinder");
var options = new CmdOptions_1.CmdOptions();
options.add("target", "ES5", ["--target"], "ES3, ES5, ES6, ES2015 or Latest. Default is ES5.");
options.add("outDir", "hxclasses", ["--out-dir"], "Output directory. Default is 'hxclasses'.");
options.add("rootPackage", "", ["--root-package"], "Root package for generated classes. Default is empty.");
options.add("nativeNamespace", "", ["--native-namespace"], "Prefix package for @:native meta.");
options.add("logLevel", "warn", ["--log-level"], "Verbose level: 'none', 'warn' or 'debug'. Default is 'warn'.");
options.addRepeatable("imports", ["--import"], "Add import for each generated file.");
options.addRepeatable("typeMappers", ["--type-mapper"], "Add type mapper file.");
options.addRepeatable("typedefs", ["--typedef"], "Export specified interface as haxe typedef.");
options.addRepeatable("typedefFiles", ["--typedef-file"], "Like `--typedef` but read type names from file (one type on line).");
options.addRepeatable("filePaths", null, "Source typescript definition file path or directory.");
if (process.argv.length <= 2) {
    console.log("TypeScript definition files (*.d.ts) to haxe convertor.");
    console.log("Usage: dts_to_haxe <options> <filePaths> ...");
    console.log("Options:");
    console.log(options.getHelpMessage());
    console.log("Type mapper file format: one statement per line. Statements:");
    console.log("\tTypeA => TypeB // use type TypeB where TypeA originally used");
    console.log("\tMyClass@myFunc => Int // use return type `Int` for specified class and function");
    console.log("\t@myFunc => Int // use return type `Int` for all functions named `myFunc`");
    console.log("\t@myFunc => Int if Float // use return type `Int` for all functions named `myFunc` where original type is `Float`");
    console.log("\t*myVar => Int if Float // use `Int` instead `Float` for all variables named `myVar`");
    console.log("\t@myMethod.myParam => Int if Float // use `Int` instead `Float` for all parameters named `myParam` for methods");
    process.exit(1);
}
var params = options.parse(process.argv.slice(2));
const compilerOptions = {
    target: ts.ScriptTarget.ES5,
    module: ts.ModuleKind.CommonJS,
    noLib: true
};
switch (params.get("target").toUpperCase()) {
    case "ES3":
        compilerOptions.target = ts.ScriptTarget.ES3;
        break;
    case "ES5":
        compilerOptions.target = ts.ScriptTarget.ES5;
        break;
    case "ES6":
        compilerOptions.target = ts.ScriptTarget.ES6;
        break;
    case "ES2015":
        compilerOptions.target = ts.ScriptTarget.ES2015;
        break;
    case "LATEST":
        compilerOptions.target = ts.ScriptTarget.Latest;
        break;
    default:
        console.log("Unknow target.");
        process.exit(1);
}
let filePaths = params.get("filePaths");
for (var i = 0; i < filePaths.length; i++) {
    if (fs.statSync(filePaths[i]).isDirectory()) {
        var allFiles = [];
        FsTools.walkSync(filePaths[i], (start, dirs, files) => allFiles = allFiles.concat(files.filter(x => x.endsWith(".d.ts")).map(x => start + "/" + x)));
        var after = filePaths.slice(i + 1);
        filePaths = filePaths.slice(0, i).concat(allFiles).concat(after);
        i += allFiles.length - 1;
    }
}
var typeMapperData = new Map();
for (let fileName of params.get("typeMappers")) {
    for (let line of FsTools.readTextFileLines(fileName, true, true)) {
        var p = line.split("=>");
        if (p.length == 2)
            typeMapperData.set(p[0].trim(), p[1].trim());
        else
            console.log("ERROR in file '" + fileName + "': bad string '" + line + "'.");
    }
}
var typeMapper = new TypeMapper_1.TypeMapper(typeMapperData);
var typedefs = params.get("typedefs");
for (let fileName of params.get("typedefFiles")) {
    typedefs = typedefs.concat(FsTools.readTextFileLines(fileName, true, true));
}
const program = ts.createProgram(filePaths, compilerOptions);
const typeChecker = program.getTypeChecker();
var results = new Array();
var knownTypes = new Array();
for (let sourceFile of program.getSourceFiles()) {
    let finder = new DtsFilePossibleTypesFinder_1.DtsFilePossibleTypesFinder(sourceFile, params.get("rootPackage"));
    knownTypes = knownTypes.concat(finder.find());
}
for (let sourceFile of program.getSourceFiles()) {
    console.log("Process file " + sourceFile.path);
    let parser = new DtsFileParser_1.DtsFileParser(sourceFile, typeChecker, typeMapper, params.get("rootPackage"), params.get("nativeNamespace"), typedefs, knownTypes);
    parser.parse(results, new Logger_1.Logger(params.get("logLevel")));
}
for (var klass of results) {
    klass.addImports(params.get("imports"));
    let destFilePath = params.get("outDir") + "/" + TypePathTools_1.TypePathTools.normalizeFullClassName(klass.fullClassName).split(".").join("/") + ".hx";
    console.log("Save file " + destFilePath);
    FsTools.mkdirp(path.dirname(destFilePath));
    fs.writeFileSync(destFilePath, klass.toString());
}
//# sourceMappingURL=DtsToHaxe.js.map