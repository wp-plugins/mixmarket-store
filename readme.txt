=== Mixmarket (Каталог товаров) ===
Contributors: Mixmarket
Tags: plugin, mixmarket, partner network, catalogue goods, affiliate, ads, ad
Requires at least: 2.9
Tested up to: 3.1
Stable tag: trunk

Плагин для создания каталога товаров и размещения рекламы сервиса товарной рекламы Микс-Товары

== Description ==

Плагин для создания каталога товаров и размещения рекламы сервиса товарной рекламы Микс-Товары. <a href=
"http://blog.mixmarket.biz/wordpress-plugin-mixmarket/?from=wordpress">Подробная инструкция по настройке данного плагина >></a>

The Mixmarket plugin is elegant and easy way to create catalog of goods and integrate it with mixmarket affiliate network.

The main purpose of this plugin is easy creation of goods catalogue.

It allows:

* Group goods in nested categories
* Set unlimited number of additional fields for each category
* Generate separate search forms for every category, based on its additional fields
* Search goods based on main and additional fields
* Mark goods as featured
* Generate pages based on goods category, brand and featured mark
* Show widget in your sidebar with goods from any category
* Export and import catalogue data.


== Installation ==

1. Download the zip file.
2. Extract `mixmarket` folder.
3. Upload `mixmarket` folder to your` wp-content/plugins` directory.
4. Log in to your WordPress blog.
5. Create an empty Page (title may be any). This Page will be an entry point of your catalog.
6. Click on "Plugins" menu.
7. Locate the "MixMarket" plugin and click "Activate". "MixMarket" menu will appear at the bottom of right panel.
8. Go to MixMarket -> Options and set values as you like. Pay special attention to fields "Catalog page" and "Partner Id". The former should point to the Page, which was created on step 5. The later should be valid partner Id, which can be received on mixmarket.biz.
9. Go to MixMarket -> Categories and create at least one category.
10. Now you can add Goods (MixMarket -> Items).
11. Optional. Go to Appearance -> Widgets and add Mixmarket widget to appropriate sidebar. Select the category and number of items in the widget settings.

Important!

Please, when you add goods pay special attention at "brand" and "model" fields. They are used for searching prices in mixmarket database.

Additional fields format.

Please, look at following example.

[interface]
type=select
title=Interface
values=USB|PS/2

Each field consists of several lines with it settings.
The first line should contain field name and be taking in square brackets. Use only Latin letters, digits or "_" symbol here.
In the next line a field type should be defined. You can use following values: text, select, checkbox or textarea.
In the third line we define title parameter. Generally speaking, it is a label for the field. You can use any string as its value.
All previous parameters are required.
The last parameter – values – is required only for select field. Specify all values of the list and separate them with "|" symbol.

== Screenshots ==


== Changelog ==

