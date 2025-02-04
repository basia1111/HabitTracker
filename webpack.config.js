const path = require("path");
const Encore = require("@symfony/webpack-encore");

Encore.setOutputPath("public/build/")
  .setPublicPath("/build")
  .enableSingleRuntimeChunk()
  .addEntry("app", "./assets/app.js")
  .addStyleEntry("bootstrap-css", "bootstrap/dist/css/bootstrap.min.css")
  .addStyleEntry("app-css", "./assets/styles/app.scss")
  .enableSassLoader((options) => {
    options.sassOptions = {
      indentedSyntax: false,
    };
  })

  .autoProvidejQuery();

module.exports = Encore.getWebpackConfig();
