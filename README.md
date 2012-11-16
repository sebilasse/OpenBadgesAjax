# OpenBadgesAjax

Simple testcase for Mozilla Persona and Open Badges. Uses AJAX (dojo toolkit) and php to award and issue badges into Mozilla Open Badges Infrastructure.
This is a one page example for logging in with BrowserID and earning a badge issued by you. I might add more sophisticated examples later.


## About

This is a very simple implementation of logging in with BrowserID as well as awarding and issuing badges. It uses Mozilla's issuer api javascript. Because it writes records to a text file, it really is not meant to be used for issuing many badges on a heavy production site. But because it is so simple, it requires very little set-up. Almost a plug and play. This is a starting point and can easily be modified and scaled. Simply have fun with badges!!!

## Demo
[online](http://sebastianlasse.de/dev/OpenBadgesAjax/)

## References

https://github.com/mozilla/openbadges/wiki/

https://groups.google.com/d/forum/openbadges

https://github.com/Codery/badge-it-gadget-lite

## Requirements

These scripts have been tested on a CentOS server running Apache 2.0 and PHP 5.3.2. Version PHP 5 and up should work fine.

## Instructions

1. Place the OpenBadgeAjax directory in a public directory on your web host. Ex: www.yourdomain.com/OpenBadgeAjax
2. In www.yourdomain.com/OpenBadgeAjax/settings.php make your settings changes and add your badges.
3. Set permissions for digital-badges/issued/badge_records.txt and the digital-badges/issued/json directory to rwxrwxrwx (chmod 777).
4. You may need to update your existing .htaccess file in the public root directory of your host (where your index file is) because your host's apache settings may not recognize .json files (your badge assertions). You'll know this to be the case if the issuer api returns a content type error when you issue a badge. In the existing .htaccess file, or create a new one if you don't have one, and add this line:
<pre>AddType application/json .json</pre>
5. In a browser window navigate to www.yourdomain.com/OpenBadgeAjax/

6. Give yourself a badge!! (Really - there's a badge in there for you.)

## Copyright and License

Copyright (c) 2012 Sebastian Lasse - [sebastianlasse.de](http://sebastianlasse.de/)

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.