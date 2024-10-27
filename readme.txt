=== AmazonPress ===
Contributors: Tom Makela, Warkior
Donate link: http://cj7.co.uk/amazonpress-wp-plugin/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
FAQ link: http://cj7.co.uk/amazonpress-wp-plugin/
Tags: amazon, affiliate amazon, amazon affiliate program, amazon affiliate plugin, automatic content, amazon product, amazon content, monetize related content, amazon widget plugin, related amazon products, amazonpress plugin, amazonfeed, sidebar widget, affiliate products, amazon widget wordpress, amazon product feed, amazon product api, amazon product advertising, wordpress plugin amazon, amazon wordpress plugin, amazon affiliate site, amazon advertising api, automatic amazon products, multisite, make money, Money Maker, afiliate
Requires at least: 2.3.2
Tested up to: 3.3.2
Stable tag: 9.1.1

Automatically get related affiliate products to posts & pages from Amazon.com & partner sites, CN,IT,ES,DE,UK,JP,FR,CA. NEW Amazon API 2012-06-05.

== Upgrade Notice ==
Updates & support can be found at: http://cj7.co.uk/amazonpress-premium-wp-plugin/ - http://wordpress.org/extend/plugins/amazonpress/

Original Amazonfeed Author: Warkior, WarkenSoft Productions 

== Screenshots ==

1. **screenshot-1** - Automatic products in Post/Page action related to antioxidants.
2. **screenshot-2** - Sidebar Widget in Action. Specify your widget Tags.
3. **screenshot-3** - Options settings Tab 1 - You need an Amazon Affiliate ID and Amazon API.
4. **screenshot-4** - Options settings Tab 2 - Search & related content settings.
5. **screenshot-5** - Options settings Tab 3 - Content & product display settings 
6. **screenshot-6** - Options settings Tab 4 - Cache settings, Clear cache or block specific products.

== Frequently Asked Questions ==

= What do I do next? =
Install the plugin, decide if you want to show products related to your Categories or Tags & how many. The plugin will do the rest. Continue writing about your favorite subjects, add relevant tags & the plugin will offer related products based on those tags to your visitors.
Edit Amazonpress tags in posts individually if you want more targeted products to show.

= Is there any template tags I can use to put the related Amazon products directly on the template? =
To display a list of related products in a template, you would add code like the following to your active template.
Use PHP beginning call:  
?php
$aFeed = new AmazonPress();
$aFeed->display('keyword, another keyword', true);
?
And ending php..

= Where is Data kept? =
In the SQL DB.

== Description ==
AmazonPress automatically adds products and extra content to your WordPress blog and turn it into a money making machine. By showing related Amazon products you can add related content for your site visitors enjoyment and receive up to 15% in referral fees. 

AmazonPress will automatically search for and display related products from Amazon partner sites (USA, Japan, China, Italy, Spain, Germany, France, UK, CA), which are very related to the topics you write about.

You will truly be able to focus on creating content for your niche blog, and grab some nice Amazon Affiliate comissions. 
You probably wont get rich quickly, but if you have a passion for something and want to do some extra revenue to support it, you wont regret installing it. Or build 10 or more nice blogs with plenty of updated content and you could get a monthly gift card  from Amazon or even a monthly check.

= Chinese =
このプラグインは、ワードプレスの website turn to or お金マシンとして活用 blog、 あなたのサイト訪問者の楽しみのための魅力的なコンテンツを追加したり、 紹介料の最大15％を受け取る related Amazon の商品を示すことで可能になります。これは、自動的に検索し、 Amazon.com とAmazon のパートナーサイト（アマゾンジャパン、 アマゾンドイツ、アマゾンフランス、 米国、英国、カナダ）、 具体的には書かれた内容に関連しているから関連した商品を表示します。

= French =
Ce plugin vous permettra de transformer votre site Web ou blog WordPress dans une machine à faire de l'argent, en montrant les produits liés Amazon vous pouvez ajouter du contenu attrayant pour les visiteurs du site votre plaisir et recevoir jusqu'à 15% de frais de renvoi. Il recherche automatiquement et afficher les produits connexes de Amazon.com et les sites partenaires Amazon (USA, Japan, China, Italy, Spain, Germany, France, UK, CA), qui sont spécifiquement liées aux thèmes que vous écrivez.

= Japanese =
這個插件可以讓你把你的WordPress的網站或博客成為一個賺錢的機器，通過展示相關的亞馬遜產品，您可以添加引人注目的內容為您的網站訪問者的享受，最高可獲得 15％的介紹費。它會自動搜索並顯示相關的產品和亞馬遜 Amazon.com的合作夥伴網站（亞馬遜日本，德國亞馬遜，亞馬遜法國，美國，英國，加拿大），這是特別相關的主題你寫。

= German =
Dieses Plugin ermöglicht es Ihnen, Ihre WordPress-Website oder Blog wiederum in eine Geldmaschine, indem er im Zusammenhang mit Amazon-Produkte können Sie ansprechende Inhalte für die Besucher Ihrer Website hinzuzufügen Genuss und erhalten bis zu 15% Werbekostenerstattung. Es wird automatisch für die Suche und Anzeige von verwandten Produkten und Amazon.com Amazon Partner-Websites (Amazon Japan, China, Italy, Spain, Amazon Deutschland, Amazon Frankreich, USA, Großbritannien, Kanada), die speziell auf die Themen, die Sie schreiben über verbunden sind.

== Installation ==
= Plugin Requirements: =

* WordPress 2.3 or higher
* PHP 5.1.x or higher
* An Amazon Associates Tag ( [http://affiliate-program.amazon.com/gp/associates/join](http://affiliate-program.amazon.com/gp/associates/join/?_encoding=UTF8&tag=xxxxxxxus-20) )
* An Amazon Web Services Access Key ([http://aws.amazon.com](http://aws.amazon.com/?_encoding=UTF8&tag=xxxxxxxus-20) )

= Instructions: =

= 1. Get an Amazon Associates Tag =
* You will first need to apply for an affiliate account with Amazon.com or Amazon partner site.  This will allow you to receive rewards based on products you refer to others.
* You can create your Amazon Associates account at [http://affiliate-program.amazon.com/gp/associates/join](http://affiliate-program.amazon.com/gp/associates/join/?_encoding=UTF8&tag=xxxxxxxus-20).

= 2. Get an Amazon Web Services Access Key and Security Key =
* In order to run this sort of web service on your site, you will need to register for an API called Amazon Web Services Access Key.  This will allow you access to customize and display a range of Amazon products on your site.  More importantly, you need it for the plugin to work.
* You can get your access key from Amazon Web Services by joining the community at [http://aws.amazon.com](http://aws.amazon.com/?_encoding=UTF8&tag=xxxxxxxus-20). You’ll be given an AWS key to use in all your web service requests after you register.

= 3. Upload the Plugin Files =
* Add through repository or the WP-Admin plugin upload feature and upload the amazonpress.zip file.
* or Unzip and upload the /amazonpress/ folder and files to your WordPress plugins folder, located in your WordPress install under /wp-content/plugins/

= 4. Activate the Plugin =
* Browse in the WordPress admin interface to the plugin activation page and activate the AmazonPress plugin.

= 5. Configure Management Options =
* The plugin should now be installed and activated however no products will be displayed on your site quite yet.  The first time you visit management page, the appropriate tables will be created in the database if necessary.
* On this page you will need to enter the Amazon Associates Tag and Amazon Web Services Access Key you registered for above.  You can also configure various aspects of how the plugin functions such as the number of results to show, and so on.
* Once you save these options, the products will begin to be displayed on your site related to the topics of your posts.
* In WordPress 2.7.x and higher, the AmazonPress management page is located under the Tools menu on the left hand side of the WordPress admin interface. In WordPress versions before 2.7 it is located on the AmazonPress page under the Management TAB along the top of the admin navigation.

= 6. Tell Us What You Think =
* Ok, this part is optional, but we would love to hear how AmazonPress is helping you, making bonus cash. Ways you can show the love are to like our [FB Fan Page](https://www.facebook.com/Amazon.Press), [leave your comments on our blog](http://cj7.co.uk/amazonpress-premium-wp-plugin/), link to us, or [rate us on the WordPress.org plugin page](http://wordpress.org/extend/plugins/amazonpress/). 

== Features ==
Automatically advertise related products from Amazon.com and Amazon partner sites (CN,IT,ES,US,DE,JP,FR,CA,UK), showing products specifically related to the topic of your posts. Make extra money in the process.

* Provide valuable additional content to your visitors on the topics of your posts.

* Automatically load category or tag related products from all Amazon.com or partner sites.

* Earn rewards as an affiliate, simply by entering your affiliate tracking code and API.

* Total control to tailor results for any given post.

* Excellent content caching for lightning fast response times.

* Ability to disable related products from being displayed at all on any given post.

* NOT JavaScript based, meaning it is not hidden from users with no JavaScript capability.

* Easily managed sidebar widget support.

* Many more great features you'll just have to explore.

= Chinese =
从亚马逊自动广告合作伙伴网站（美国， 署署长，太平绅士，阻燃，CA）的显示产品，具体涉及到的相关帖子主题产品。轻松添加额外的内容类别或标签上的产品为基础，并在这个过程中多余的钱。亚马逊API的要求。
= German =
Automatisch Werben verwandte Produkte von Amazon Partner-Sites (CN,IT,ES,US,DE,JP,FR,CA,UK), zeigt Produkte, die speziell auf das Thema Ihrer Beiträge in Zusammenhang stehen. Einfaches Hinzufügen von zusätzlichen Inhalten zu Produkten auf Basis Kategorie oder Tags basieren und machen zusätzliches Geld in den Prozess. Amazon-API erforderlich.

= Japanese =
自動的にアマゾンのパートナーサイト（米国、ドは、JP、のFR、カナダ）、製品を示すから関連した商品を具体的にあなたの記事のトピックに関連する広告を掲載。簡単に追加のコンテンツ、製品カテゴリーやタグに基づいて基づいて追加して、プロセス内の余分なお金を稼ぐ。アマゾンのAPIが必要です。

= French =
Automatiquement la publicité de produits connexes provenant de sites partenaires Amazon (CN,IT,ES,US,DE,JP,FR,CA,UK), qui présentent des produits spécifiquement liés au thème de vos messages. Facilement ajouter du contenu supplémentaire à base de produits basés sur la catégorie ou des balises et de faire de l'argent supplémentaire dans le processus. L'API d'Amazon nécessaire.

== More Information ==
For more information about this plugin version, visit the plugin homepage at:
[http://cj7.co.uk/amazonpress-premium-wp-plugin/](http://cj7.co.uk/amazonpress-premium-wp-plugin/)

Even though it is capable of being fully automatic in this way, it also includes powerful controls to tailor exactly how it functions. You can customize the keywords used for searching on any given post or even disable the plugin completely. You can also customize the heading that is shown, the number of results shown and many other aspects of how it functions.

* Known Bug: We reccomend that you check/ empty the cache manually once in a month, it sometimes hangs up.

* Known Bug: Odd characters in description might sometimes hang the content feed. Current solution: Manually empty cache.

* Warning: Do not use too many amazon products and high cache time as that can utilize alot of MYSQL disk space if you have more than 10.000 posts. The more related posts the higher risk.

== Note ==
AmazonPress  Version - 9.1.1  -  includes Amazon.com/USA, China, Italy, Spain, Japan, Germany, France, United Kingdom & Canada Partner sites.
Improved by: Tom Makela adding the extra countries.
This was the honest and upgraded "Amazonfeed" and not as a couple of plugin thieves stated a few months back as their original creation, this is a poke in their eye as they did not properly credit the original creator and hid the Tip Feature for revenue sharing and not mention about the fact that there is a Tip feature in the plugin for anyone who installs it.

Plugin is based on Amazonfeed 2.0.1 (created by warkensoft.com), with my enhancements giving Amazon DE, JP, FR functionality = Therefore I name it AmazonPress 8.6 and then 9.0.1 as it is a fork of AmazonFeed (and to distinc between the original AmazonFeed plugin, and as I have further plans and ideas for it).

== Changelog ==
= 9.1.1 =
* Autoupdate fix
* Minor typofixes
* Admin interface has got a few changes.

= 9.1 =
* New Amazon API
* Has Support for USA, China, Italy, Spain, Japan, Germany, France, United Kingdom and Canada Partner sites.

= 9.0.5 =
* "Not Publicly released"
* Has Support for USA, Italy, Spain, Japan, Germany, France, United Kingdom, Canada based Amazon Partner sites.

= 9.0.1 =
* Added Support for Germany (Tom Makela - fork re-named/published as AmazonPress)

= 8.7 =
* Not publicly released.
* Minor bug fixes in code.

= 8.6 =
* Not publicly released.
* Added Support for France, Japan (Tom Makela - fork re-named to AmazonPress)

= 2.0 =
* Last known release by Warkior.
* Some Amazon related stuff has been fixed

= 1.9 =
* Added ability to sort results on the whole blog, as well as on individual posts.
* Added ability to chose display position relative to page/post content.
* Reworked product caching for more robust storage and more advanced potential future features.
* Added ability to block specific products from being displayed.

= 1.8 =
* Added sidebar Widget controls.
* Enabled display of product descriptions.
* Updated display CSS for better theme compatibility.
* Expanded debugging and error control.

= 1.7 =
* Expanded debugging and error control.
* Allowed display of cached Amazon data in admin console.

= 1.6 =
* Minor bug fixes when zero results are returned.
* Better admin screen organization.
* Upgraded admin security.
* Allowed display of only images or only text.
* Allowed for custom stylesheets.

= 1.5 =
* Minor tweaks to allow for a greater range of Amazon access keys.  Not a necessary upgrade if it already works for you.

= 1.4 =
* Updated to support new Amazon security protocols.

== License Notes ==
AmazonPress 9.1 is free software and can be aquired from Wordpress repository; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; version 3 of the License.

AmazonPress 9.1 Premium IS NOT Free to re-distribute, nor to re-sell. It is an enhanced version compared to the version available in wordpress repository version with additional support.

AmazonPress 9.1 is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

AmazonPress 9.1 Premium is sold in the hope that it will be useful and can generate money for those who has paid for it, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

== BETA SOFTWARE WARNING ==
Please be aware as you use this software that it is still considered to be BETA SOFTWARE and as such may function in unexpected ways.  Of course, we do try our best to make sure it is as stable as possible and try to address problems as quickly as possible when they come up, but just be aware that there may still be bugs. If you find bugs please report them.