declare module BABYLON {
	class Node {}
	
    interface Behavior<T extends Node> {
        name: string;
        init(): void;
        attach(node: T): void;
        detach(): void;
    }
}