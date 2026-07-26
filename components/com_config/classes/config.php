<?php

class config
{
    static $table =  __prefixe_db__ . "config";
    static $table2 =  __prefixe_db__ . "details_config";

    private $nom;
    private $titre;
    private $description;
    private $logo;
    private $email;
    private $tel;
    private $tel2;
    private $fax;
    private $adresse;
    private $x;
    private $y;
    private $id_slider;
    private $facebook;
    private $twitter;
    private $youtube;
    private $instagram;
    private $pinterest;
    private $linkedin;
    private $snapshat;
    private $tumblr;
    private $viadeo;
    private $analytic;
    private $condition_paiment;

    public function __construct($db, $lang = 'fr')
    {

        $SQLselect = "SELECT A.*, B.* FROM " . __prefixe_db__ . "config A
					  LEFT JOIN " . __prefixe_db__ . "details_config B ON A.id = B.id_config AND langue = '$lang'
					  WHERE A.id = 0";

        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {

            $data = $db->fetch_assoc($result);
            $this->nom = $data['nom'];
            $this->titre = $data['titre'];
            $this->description = $data['description'];
            $this->logo = $data['logo'];
            $this->email = $data['email'];
            $this->tel = $data['tel'];
            $this->tel2 = $data['tel2'];
            $this->fax = $data['fax'];
            $this->adresse = $data['adresse'];
            $this->x = $data['x'];
            $this->y = $data['y'];
            $this->id_slider = $data['id_slider'];
            $this->facebook = $data['facebook'];
            $this->twitter = $data['twitter'];
            $this->youtube = $data['youtube'];
            $this->instagram = $data['instagram'];
            $this->pinterest = $data['pinterest'];
            $this->linkedin = $data['linkedin'];
            $this->snapshat = $data['snapshat'];
            $this->tumblr = $data['tumblr'];
            $this->viadeo = $data['viadeo'];
            $this->analytic = $data['analytic'];
            $this->condition_paiment = $data['condition_paiment'];
        }
    }

    public function __destruct()
    {
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIdSlider()
    {
        return $this->id_slider;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function getTel2()
    {
        return $this->tel2;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getFacebook()
    {
        return $this->facebook;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function getYoutube()
    {
        return $this->youtube;
    }

    public function getInstagram()
    {
        return $this->instagram;
    }

    public function getPinterest()
    {
        return $this->pinterest;
    }

    public function getLinkedin()
    {
        return $this->linkedin;
    }

    public function getSnapshat()
    {
        return $this->snapshat;
    }

    public function getTumblr()
    {
        return $this->tumblr;
    }

    public function getViadeo()
    {
        return $this->viadeo;
    }

    public function getAnalytic()
    {
        return $this->analytic;
    }

    public function getConditionPaiment()
    {
        return $this->condition_paiment;
    }

    public function getAnalytics()
    {
        return "<script>   
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');    ga('create', '" . $this->analytic . "', 'auto');   ga('send', 'pageview');  
		</script>";
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;
    }

    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public function setSlide($slider)
    {
        $this->id_slider = $slider;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;
    }

    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
    }

    public function setSnapchat($snapshat)
    {
        $this->snapshat = $snapshat;
    }

    public function setPinterest($pinterest)
    {
        $this->pinterest = $pinterest;
    }

    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;
    }

    public function setTumblr($tumblr)
    {
        $this->tumblr = $tumblr;
    }

    public function setViadeo($viadeo)
    {
        $this->viadeo = $viadeo;
    }

    public function setAnalytic($analytic)
    {
        $this->analytic = $analytic;
    }

    public function setConditionPaiment($condition_paiment)
    {
        $this->condition_paiment = $condition_paiment;
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET logo=%s, email=%s, tel=%s, tel2=%s, fax=%s, x=%s, y=%s, id_slider=%s, facebook=%s, twitter=%s, youtube=%s, instagram=%s, pinterest=%s, linkedin=%s, snapshat=%s, tumblr=%s, viadeo=%s, analytic=%s, condition_paiment=%s WHERE id = %s",
            GetSQLValueString($this->logo, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->tel, "text"),
            GetSQLValueString($this->tel2, "text"),
            GetSQLValueString($this->fax, "text"),
            GetSQLValueString($this->x, "text"),
            GetSQLValueString($this->y, "text"),
            GetSQLValueString($this->id_slider, "int"),
            GetSQLValueString($this->facebook, "text"),
            GetSQLValueString($this->twitter, "text"),
            GetSQLValueString($this->youtube, "text"),
            GetSQLValueString($this->instagram, "text"),
            GetSQLValueString($this->pinterest, "text"),
            GetSQLValueString($this->linkedin, "text"),
            GetSQLValueString($this->snapshat, "text"),
            GetSQLValueString($this->tumblr, "text"),
            GetSQLValueString($this->viadeo, "text"),
            GetSQLValueString($this->analytic, "text"),
            GetSQLValueString($this->condition_paiment, "text"),
            GetSQLValueString(0, "int")
        );
        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf(
                "SELECT * FROM " . static::$table2 . " WHERE id_config = %s AND langue = %s",
                GetSQLValueString(0, "int"),
                GetSQLValueString($_SESSION['langue'], "text")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf(
                    "INSERT INTO " . static::$table2 . " (id_config, nom, titre, description, adresse, langue) VALUES (%s, %s, %s, %s, %s, %s)",
                    GetSQLValueString(0, "int"),
                    GetSQLValueString($this->nom, "text"),
                    GetSQLValueString($this->titre, "text"),
                    GetSQLValueString($this->description, "text"),
                    GetSQLValueString($this->adresse, "text"),
                    GetSQLValueString($_SESSION['langue'], "text")
                );
            } else {
                $SQLupdate = sprintf(
                    "UPDATE " . static::$table2 . " SET nom=%s, titre=%s, description=%s, adresse=%s WHERE id_config = %s AND langue = %s",
                    GetSQLValueString($this->nom, "text"),
                    GetSQLValueString($this->titre, "text"),
                    GetSQLValueString($this->description, "text"),
                    GetSQLValueString($this->adresse, "text"),
                    GetSQLValueString(0, "int"),
                    GetSQLValueString($_SESSION['langue'], "text")
                );
            }
            if (!$db->query($SQLupdate)) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 3;
        }
    }
    public function toArray()
    {
        return array(
            'email' => $this->email,
            'tel' => $this->tel,
            'tel2' => $this->tel2,
            'fax' => $this->fax,
            'adresse' => $this->adresse,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'youtube' => $this->youtube,
            'instagram' => $this->instagram,
            'pinterest' => $this->pinterest,
            'linkedin' => $this->linkedin,
            'tumblr' => $this->tumblr,
            'viadeo' => $this->viadeo,
        );
    }
}
