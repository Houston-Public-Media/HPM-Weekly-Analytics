# Weekly Analytics by Service

A PHP script that aggregates and parses analytics from various services either programmatically (Google Analytics, Facebook, Instagram, Triton Webcast Metrics, YouTube) or from a pre-run report (Twitter, Apple News). It then saves that information as a sheet in Google Drive, and as a JSON file for use in the [included graphing application](./hpm-analytics-new). Finally, it sends an email to a designated group so that they can make use of the data.

## Why does this exist?

Our executive team was interested in getting regular analytics updates, and as a busy web developer, I decided to pull together the various analytics projects I had worked on into a single place. That way, I could automate the hell out of the process. Well, most of it (*I'm looking at you, Twitter and Apple News*).

## Getting started

1. Either clone the repo or download/unzip the ZIP. Change directory into the folder.
2. Run `composer install` in your terminal of choice.
3. Copy `.env.sample` to `.env` and fill out all of the fields.
4. Save your various credential files and access tokens into the `creds` folder
5. Run `php weekly.php` to run the script
6. Answer a few questions and then get yourself a cup of coffee (**You've earned it, champ**)

## Caveats

- I wrote this script in macOS, but it should be able to run on a Windows system.
- I wrote this for a public media broadcasting entity, so all of these services might not apply to you
- I tried to thoroughly comment the various scripts, but if you have any trouble parsing it, let me know

## Wishlist

- Add options for other email/storage systems (currently only supports S3 and Amazon SES)
  - This one is contingent on demand

## Questions

Contact me at [jcounts@houstonpublicmedia.org](mailto:jcounts@houstonpublicmedia.org?subject=Weekly%20Analytics%20Script).

## License

Copyright (c) 2018 Houston Public Media

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
