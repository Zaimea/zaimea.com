# Zaimea.com

This repo contains the content of the zaimea.com website.

## Credits

This website was principally designed by [Custură Laurențiu](https://github.com/orgs/zaimea/people/custura).

## License

-   The web application falls under the [MIT License](https://github.com/zaimea/zaimea.com/blob/main/LICENSE)
-   Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Local Development

If you want to work on this project on your local machine, you may follow the instructions below.

1. Fork this repository 
2. Open your terminal and `cd` to your `~/zaimea.com` folder
3. Clone your fork into the `~/zaimea.com` folder, by running the following command *with your username placed into the {username} slot*:
    ```bash
    git clone git@github.com:{username}/zaimea.com zaimea.com
    ```
4. CD into the new directory you just created:
    ```bash
    cd zaimea.com
    ```
5. Add auth.json file with valid credentials
    ```json
    {
        "github-oauth": {
            "github.com": "your_key"
        }
    }
    ```
6. Update composer and install modules
    ```bash 
    composer update
    ```
    ```bash 
    npm install
    ```
    ```bash 
    npm run build
    ```
    ```bash 
    php artisan migrate:fresh --seed
    ```


## Check Zaimea documentation

Visit [Zaimea Developers](https://developer.zaimea.com/) to read full documentation.
