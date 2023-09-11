# hitobito mailing lists using cyon.ch mail redirects

This project helps to use the [cyon.ch](https://cyon.ch) email forwarding features in combination with [hitobito](https://github.com/hitobito/hitobito) mailing lists. This way, very robust and dynamically updated email forwarding lists can be created.

### Deployment notes:
1. Create a new file .env as a copy of .env.example, and fill in the credentials of your setup. The API Token needs at least the permissions "Abonnent/-innen von Mailinglisten dieser Ebene" and "Personen dieser Ebene"
1. Also in your .env file, for each of your mailing lists, create a line for mapping email address to hitobito mailing list id. E.g. if somebody-hello.123@somedomain should redirect to the members of hitobito mailing list 1234, you would add the line: `LIST_ID_somebody_hello.123_somedomain.ch=1234`. **Be sure to replace `@` and any other special characters with `_` (except for `.`)!**
1. Run `composer install` inside the repository
1. Deploy all files to your cyon account, e.g. /home/\<my-cyon-username\>/cyon-hitobito-mailing-lists
1. Redirect your cyon mailing lists to the index.php script.

If you change the code, you can simulate an email locally as follows. Careful if you have productive .env credentials, the mail will really be sent out!
```
docker compose run cli
```
