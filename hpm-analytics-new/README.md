# Analytics Graphing Application

To get this up and running, change the `dlUrl` constant in `assets/main.js` to point to the folder where you have your JSON data stored. Then you upload these three files to your web server. Easy peasy.

I rewrote this graphing frontend because the previous version was using Webpack, Vue.js, jQuery, Bootstrap, etc., and it contained a lot of unnecessary complexity. I challenged myself to rewrite it in vanilla Javascript and to use Bulma for the layout. ~~The one downside (if you can call it that) is that it's no longer compatible with IE 11, since I would basically have to write a second version to support it, and I don't want to encourage anyone to keep using Internet Explorer at all.~~ Actually, I figured out a way to maintain IE compatiblity without too much effort. However, IE 11 is old and terrible and you shouldn't be using it.

The biggest upside is the final application size went from 5.1MB to 33KB. Not too shabby.