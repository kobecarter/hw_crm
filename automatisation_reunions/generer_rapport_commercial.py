"""
Génère l'onglet du jour dans "Rapport de la réunion commerciale"
à partir des factures impayées / partiellement payées, des devis en attente
et des relances non traitées du CRM.

Colonnes du modèle observé : B=Client, puis un bloc de 2 colonnes par membre
de l'équipe (Taches faites / Taches à effectuer), ex : C/D=Hibatallah, E/F=Hamid, G/H=Zakaria.

Ce script pré-remplit uniquement la colonne "Taches faites" avec un résumé
généré depuis le CRM (préfixé [Auto CRM]). La colonne "Taches à effectuer"
reste vide : c'est la décision prise en réunion.
"""
import gspread.utils

import config
import db
from sheets_utils import ouvrir_client, nouvel_onglet_depuis_modele


def nom_client(row):
    return row["raison_social"] or f'{row["prenom"] or ""} {row["nom"] or ""}'.strip() or f'Client #{row["id"]}'


def recuperer_donnees():
    conn = db.connecter(config.CHEMIN_CRM)
    resume_par_client = {}

    with conn.cursor() as cur:
        # Factures impayées ou partiellement payées
        cur.execute("""
            SELECT c.id, c.raison_social, c.prenom, c.nom,
                   f.numero, f.total, f.statu,
                   COALESCE((SELECT SUM(montant) FROM crm_payment p WHERE p.id_facture = f.id), 0) AS paye
            FROM crm_facture f
            JOIN crm_client c ON c.id = f.id_client
            WHERE f.statu IN (0, 2) AND (f.archived IS NULL OR f.archived = 0)
            ORDER BY c.id
        """)
        for row in cur.fetchall():
            client = nom_client(row)
            reste = (row["total"] or 0) - (row["paye"] or 0)
            texte = f"Facture {row['numero']} : reste {reste:.2f} ({config.STATUT_FACTURE.get(row['statu'], row['statu'])})"
            resume_par_client.setdefault((row["id"], client), []).append(texte)

        # Devis envoyés / en attente de signature
        cur.execute("""
            SELECT c.id, c.raison_social, c.prenom, c.nom,
                   d.numero, d.total, d.statu, d.date_devis
            FROM crm_devis d
            JOIN crm_client c ON c.id = d.id_client
            WHERE d.statu IN (1, 3)
            ORDER BY c.id
        """)
        for row in cur.fetchall():
            client = nom_client(row)
            texte = f"Devis {row['numero']} ({config.STATUT_DEVIS.get(row['statu'], row['statu'])}) - {row['total']}"
            resume_par_client.setdefault((row["id"], client), []).append(texte)

        # Dernière relance non traitée par client
        cur.execute("""
            SELECT r.id_client, r.remarque, r.date
            FROM crm_relance r
            WHERE r.traite = 0 OR r.traite IS NULL
            ORDER BY r.date DESC
        """)
        relances_par_client = {}
        for row in cur.fetchall():
            relances_par_client.setdefault(row["id_client"], row)

    conn.close()

    # Fusionne les remarques de relance dans le résumé
    for (id_client, client), lignes in resume_par_client.items():
        if id_client in relances_par_client:
            r = relances_par_client[id_client]
            lignes.append(f"Dernière relance ({r['date']}) : {r['remarque'] or ''}".strip())

    return resume_par_client


def responsable_de(nom_client_txt):
    for cle, resp in config.RESPONSABLE_PAR_CLIENT.items():
        if cle.strip().lower() == nom_client_txt.strip().lower():
            return resp
    return None


def generer():
    resume_par_client = recuperer_donnees()

    gc = ouvrir_client(config.CHEMIN_SERVICE_ACCOUNT)
    sh = gc.open_by_key(config.SHEET_ID_COMMERCIAL)
    ws, _ = nouvel_onglet_depuis_modele(sh)

    equipe = config.EQUIPE_COMMERCIALE
    non_assignes = []
    maj = []  # liste de cell updates [{'range': 'B4', 'values': [['...']]}]

    ligne = 4  # première ligne de données observée dans le modèle
    for (id_client, client), lignes in sorted(resume_par_client.items(), key=lambda x: x[0][1]):
        resp = responsable_de(client)
        texte = "[Auto CRM]\n" + "\n".join(lignes)

        maj.append({"range": f"B{ligne}", "values": [[client]]})

        if resp in equipe:
            i = equipe.index(resp)
            col_faites = gspread.utils.rowcol_to_a1(ligne, 3 + 2 * i).rstrip("0123456789")
            maj.append({"range": f"{col_faites}{ligne}", "values": [[texte]]})
        else:
            non_assignes.append(client)
            maj.append({"range": f"C{ligne}", "values": [[texte + "\n\n(!) Client non mappé dans config.RESPONSABLE_PAR_CLIENT"]]})

        ligne += 1

    if maj:
        ws.batch_update(maj)

    print(f"Onglet '{ws.title}' créé dans le rapport commercial avec {ligne - 4} client(s).")
    if non_assignes:
        print("Clients non assignés à un responsable (à ajouter dans config.py) :")
        for c in non_assignes:
            print("  -", c)


if __name__ == "__main__":
    generer()
