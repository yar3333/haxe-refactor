/// <reference path="../typings/globals/node/index.d.ts" />

import * as fs from "fs";
import * as path from "path";
import * as ts from "typescript";
import { DtsFileParser } from "./DtsFileParser";
import { CmdOptions } from "./CmdOptions";
import { Logger } from "./Logger";
import { HaxeTypeDeclaration } from "./HaxeTypeDeclaration";
import * as FsTools from "./FsTools";
import { TypeConvertor } from "./TypeConvertor";
import { TypePathTools } from "./TypePathTools";
import { DtsFilePossibleTypesFinder } from "./DtsFilePossibleTypesFinder";

var options = new CmdOptions();
options.add("target", "ES5", ["--target"], "ES3, ES5, ES6, ES2015 or Latest. Default is ES5.")
options.add("outDir", "hxclasses", ["--out-dir"], "Output directory. Default is 'hxclasses'.")
options.add("rootPackage", "", ["--root-package"], "Root package for generated classes. Default is empty.")
options.add("nativeNamespace", "", ["--native-namespace"], "Prefix package for @:native meta.")
options.add("logLevel", "warn", ["--log-level"], "Verbose level: 'none', 'warn' or 'debug'. Default is 'warn'.")
options.addRepeatable("imports", ["--import"], "Add import for each generated file.")
options.addRepeatable("typeMappers", ["--type-mapper"], "Add mapper file.")
options.addRepeatable("typedefs", ["--typedef"], "Export specified interface as haxe typedef.")
options.addRepeatable("typedefFiles", ["--typedef-file"], "Like `--typedef` but read type names from file (one type on line).")
options.addRepeatable("filePaths", null, "Source typescript definition file path or directory.");

if (process.argv.length <= 2)
{
    console.log("TypeScript definition files (*.d.ts) to haxe convertor.");
    console.log("Usage: dts_to_haxe <options> <filePaths> ...");
    console.log("Options:");
    console.log(options.getHelpMessage());
    process.exit(1);
}

var params = options.parse(process.argv.slice(2));

const compilerOptions: ts.CompilerOptions =
{
    target: ts.ScriptTarget.ES5,
    module: ts.ModuleKind.CommonJS,
    noLib: true
};

switch (params.get("target").toUpperCase())
{
    case "ES3": compilerOptions.target = ts.ScriptTarget.ES3; break;
    case "ES5": compilerOptions.target = ts.ScriptTarget.ES5; break;
    case "ES6": compilerOptions.target = ts.ScriptTarget.ES6; break;
    case "ES2015": compilerOptions.target = ts.ScriptTarget.ES2015; break;
    case "LATEST": compilerOptions.target = ts.ScriptTarget.Latest; break;
    default: console.log("Unknow target."); process.exit(1);
}

let filePaths : Array<string> = params.get("filePaths");
for (var i = 0; i < filePaths.length; i++)
{
    if (fs.statSync(filePaths[i]).isDirectory())
    {
        var allFiles = [];
        FsTools.walkSync(filePaths[i], (start, dirs, files) => allFiles = allFiles.concat(files.filter(x => x.endsWith(".d.ts")).map(x => start + "/" + x)));
        var after = filePaths.slice(i + 1);
        filePaths = filePaths.slice(0, i).concat(allFiles).concat(after);
        i += allFiles.length - 1;
    }
}

var typeMapper = new Map<string, string>();
for (let fileName of params.get("typeMappers"))
{
    for (let line of FsTools.readTextFileLines(fileName, true, true))
    {
        var p = line.split("=>");
        if (p.length == 2) typeMapper.set(p[0].trim(), p[1].trim());
        else console.log("ERROR in file '" + fileName + "': bad string '" + line + "'.");
    }
}
var typeConvertor = new TypeConvertor(typeMapper);

var typedefs : Array<string> = params.get("typedefs");
for (let fileName of params.get("typedefFiles"))
{
    typedefs = typedefs.concat(FsTools.readTextFileLines(fileName, true, true));
}

const program = ts.createProgram(filePaths, compilerOptions);
const typeChecker = program.getTypeChecker();

var results = new Array<HaxeTypeDeclaration>();

var knownTypes = new Array<string>();
for (let sourceFile of program.getSourceFiles()) {
    let finder = new DtsFilePossibleTypesFinder(sourceFile, params.get("rootPackage"));
    knownTypes = knownTypes.concat(finder.find());
}

for (let sourceFile of program.getSourceFiles()) {
    console.log("Process file " + sourceFile.path);
    let parser = new DtsFileParser(sourceFile, typeChecker, typeConvertor, params.get("rootPackage"), params.get("nativeNamespace"), typedefs, knownTypes);
    parser.parse(results, new Logger(params.get("logLevel")));
}

for (var klass of results)
{
    klass.addImports(params.get("imports"));

    let destFilePath = params.get("outDir") + "/" + TypePathTools.normalizeFullClassName(klass.fullClassName).split(".").join("/") + ".hx";
    console.log("Save file " + destFilePath);
    FsTools.mkdirp(path.dirname(destFilePath));
    fs.writeFileSync(destFilePath, klass.toString());
}
