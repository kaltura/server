## Kaltura node.js API Client Library.
Compatible with Kaltura server version 10.20.0 and above.

## Creating a new NPM package
- Install latest npm [old versions have issues logging into the npmjs repo
- Edit package.json and inc the version to the latest API schema
- If new deps were added, add them to the dependencies section, '*' means any version is accepted, if a specific version or range of versions is needed see:
```
$ man 7 semver
```
or: https://docs.npmjs.com/files/package.json
- Run ```npm login``` and supply the kaltura account credentials
- Run ```npm publish``` to push the new package
- See new package under https://www.npmjs.com/package/kaltura
- Install the new package and test it some to ensure it was correctly packaged and is working:)
