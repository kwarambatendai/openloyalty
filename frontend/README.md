## Requirements:

nodejs >= `4.4.3`

npm >= `3.6.0`


1. Install dependencies: `npm install` (or yarn install  --ignore-engines)
2. Generate config: `gulp config` (use `--prod` flag to auto choose defaults)
3. Build front: `gulp compile`

Typical problems: 

_npm install failed due to node-sass exeption_

**Solution:** 

on unix create symlink:  `ln -s /usr/bin/nodejs /usr/bin/node`


**How to add custom directive**
1. Create new directory in `frontend/src/component/global/{dir_name}`
2. If directive has own template, place it in directory:  `frontend/src/component/global/{dir_name}/templates/` (!important)
3. Create class definition (See `DatepickerDirective.js`)
4. Import created class in `appAdmin.js` or `appCustomer.js` or `appSeller.js` or in all.
5. Register directive in `app*.js` file (e.g. `.directive('datepicker', () => new DatepickerDirective())` - it's necessary to start directive name with lowercase)
6. If directive wraps external library, install this lib via npm (or yarn) and place in index.html between `<!-- build:js assets.js -->` and `<!-- endbuild -->`

**Debug mode**
To enable console warns, logs, infos set `debug` variable to `true` when `gulp config` command will ask about it.
Debug mode also grants you access to `#!/debug` subpage witch provides some development tools.