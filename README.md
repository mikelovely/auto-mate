# Auto Mate

## Introduction
The final step to setting up your VirtualHost. Auto Mate is one command that will
- Create a config file under sites-available with VirtualHost information
- Symlink config file to sites-enabled
- Create a pow file
- Restart Apache

## Example
`sudo php auto-mate new:project foo --sub=public_html`
- foo being the name of your project
- public_hmtl is the document root of your project (optional)

All you need to do beforehand, is pull down the repository you're working on and don't forget to `powder up` when you're done.