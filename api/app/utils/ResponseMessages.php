<?php

namespace App\Utils;

class ResponseMessages
{
    private static $messages = [
        'authRequired' => 'Vous devez vous connecter pour accéder à cette ressource!',
        'credentialsFail' => 'Votre nom d\'utilisateur/e-mail ou votre mot de passe est incorrect',
        'credentialsOk' => 'Vous vous êtes connecté avec succès',
        'generalError' => "Quelque chose c'est mal passé. Merci d'essayer plus tard!",
        "fieldRequired" => "Les champs suivants sont requis: ",
        "accountCreated" => "Merci d'avoir choisi HellWorld Agency. Votre compte a été créé avec succès.",
        'noDataFound' => 'Aucune donnée n\'a été trouvée, veuillez réessayer plus tard!',
        'accountUpdated' => 'Votre compte a été mis à jour avec succès',
        'accountDeleted' => 'Votre compte a été supprimé avec succès',
        'noUserFound' => 'Aucun utilisateur n\'a été trouvé, veuillez réessayer plus tard!',
        'mailNotSent' => 'Le mail n\'a pas pu être envoyé, veuillez réessayer plus tard!',
        'noEmail' => 'Il paraît que vous n\'avez pas renseigné d\'adresse email',
        'reclamationAdded' => 'Votre reclamation a été ajoutée avec succès',
        'mailSent' => 'Votre mail a été envoyé avec succès',
        'factureDoesntBelong' => 'Cette facture ne vous appartient pas!',
        'contactMailSent' => 'Votre message a été envoyé avec succès',
        'emailExists' => 'Cette adresse email existe déjà',
        'factureIdRquired' => 'Vous devez renseigner l\'id de la facture',
        'devisIdRquired' => 'Vous devez renseigner l\'id du devis',
        'devisDoesntBelong' => 'Ce devis ne vous appartient pas!',
        'devisAlreadyAccepted' => 'Ce devis a déjà été validé!',
        'noClientRelatedDevis' => 'Aucun client n\'est lié à ce devis!',
        'noClientRelatedFacture' => 'Aucun client n\'est lié à cette facture!',
        'resetPassCodeSent' => 'Un mail de réinitialisation de mot de passe vous a été envoyé à votre adresse email',
        'WrongCode' => 'Le code de réinitialisation de mot de passe est incorrect',
        'codeExpired' => 'Le code de réinitialisation de mot de passe a expiré',
        'passReseted' => 'Votre mot de passe a été réinitialisé avec succès',
        'passwordNotMatch' => 'Les mots de passe ne correspondent pas',
        'passwordChanged' => 'Votre mot de passe a été changé avec succès',
        'noFileUploaded' => 'Veuillez télécharger un fichier!',
        'fileExtensionNotAllowed' => 'Ce type de fichier n\'est pas autorisé',
        'fileUploadFailed' => 'Une erreur est survenue lors de l\'enregistrement de votre fichier, veuillez réessayer plus tard!',
        'testimonialSaved' => 'Merci pour votre avis ! Il sera visible après validation.',


    ];
    public static function messages($msgKey)
    {
        if (isset(self::$messages[$msgKey])) {
            return self::$messages[$msgKey];
        }
        return null;
    }
}
