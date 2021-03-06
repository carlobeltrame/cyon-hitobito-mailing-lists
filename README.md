# hitobito mailing lists using cyon.ch mail redirects

This project helps to use the [cyon.ch](https://cyon.ch) email forwarding features in combination with [hitobito](https://github.com/hitobito/hitobito) mailing lists. This way, very robust and dynamically updated email forwarding lists can be created.

### Deployment notes:
1. Create a new file .env as a copy of .env.example, and fill in the credentials of your setup. The API Token needs at least the permissions "Abonnent/-innen von Mailinglisten dieser Ebene" and "Personen dieser Ebene"
1. Create a copy of test.php.example for each mailing list and adapt the hitobito mailing list id inside it
1. Deploy all files to your cyon account, e.g. /home/<my-cyon-username>/cyon-hitobito-mailing-lists
1. **Important**: Make sure all PHP files in lists directory are executable (`chmod +x cyon-hitobito-mailing-lists/lists/*.php`)
1. Redirect your cyon mailing lists to the corresponding PHP scripts

If you change the code, you can simulate an email locally as follows. Careful if you have productive .env credentials, the mail will really be sent out!
```
docker-compose run cli
```