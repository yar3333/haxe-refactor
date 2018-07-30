rmdir /S /Q ..\..\library\scripts\dts_to_haxe
mkdir ..\..\library\scripts\dts_to_haxe\node_modules\typescript
npm run tsc & xcopy /E /Q node_modules\typescript ..\..\library\scripts\dts_to_haxe\node_modules\typescript
