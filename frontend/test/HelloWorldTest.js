'use strict';

import HelloWorld from './../src/assets/js/hello-world.js';

describe('HelloWorld test', () => {
    it('should return greetings', () => {
        let helloWorld = new HelloWorld('test');
        let greetings = helloWorld.sayHello();
        expect(greetings).toEqual('test');
    });
});
