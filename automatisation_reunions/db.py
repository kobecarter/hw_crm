"""
Connexion à la base MySQL du CRM (hw_crm).
Lit automatiquement les identifiants dans config.php pour rester
toujours synchronisé avec la config réelle du CRM (pas de mot de passe dupliqué).
"""
import re
import os
import pymysql
import pymysql.cursors


def _lire_valeur_php(contenu, nom_variable):
    m = re.search(r'\$' + nom_variable + r'\s*=\s*"([^"]*)"', contenu)
    return m.group(1) if m else None


def charger_config_crm(chemin_crm):
    """Parse config.php pour récupérer host / login / password / base."""
    chemin_config = os.path.join(chemin_crm, "config.php")
    with open(chemin_config, encoding="utf-8") as f:
        contenu = f.read()

    return {
        "host": _lire_valeur_php(contenu, "host"),
        "user": _lire_valeur_php(contenu, "login"),
        "password": _lire_valeur_php(contenu, "password"),
        "database": _lire_valeur_php(contenu, "dataBaseName"),
    }


def connecter(chemin_crm):
    cfg = charger_config_crm(chemin_crm)
    return pymysql.connect(
        host=cfg["host"],
        user=cfg["user"],
        password=cfg["password"],
        database=cfg["database"],
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )
