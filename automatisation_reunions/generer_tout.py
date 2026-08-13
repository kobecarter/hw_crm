"""
Lance la génération des 3 rapports en une seule commande :
    python generer_tout.py
"""
import generer_rapport_commercial
import generer_rapport_administratif
import generer_rapport_technique

if __name__ == "__main__":
    print("=== Rapport commercial ===")
    generer_rapport_commercial.generer()

    print("\n=== Rapport administratif ===")
    generer_rapport_administratif.generer()

    print("\n=== Rapport technique ===")
    generer_rapport_technique.generer()

    print("\nTerminé. Les 3 fichiers ont un nouvel onglet daté du jour.")
