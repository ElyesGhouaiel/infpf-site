# 💾 Configuration des Backups Automatiques

## 🎯 Objectif

Sauvegarder automatiquement la base de données **tous les jours** pour prévenir toute perte de données.

---

## ✅ Ce qui est déjà fait

- ✅ Script de backup créé : `/bin/backup-database.sh`
- ✅ Compression automatique (gzip)
- ✅ Rétention 30 jours (nettoyage automatique)
- ✅ Logging complet dans `/home/u665392393/backups/backup.log`
- ✅ Script exécutable

---

## 🔧 Configuration du Cron (À FAIRE)

### 1. Éditer le crontab

```bash
crontab -e
```

### 2. Ajouter la ligne suivante

**Backup quotidien à 3h du matin :**

```bash
0 3 * * * /home/u665392393/domains/infpf.fr/dev/bin/backup-database.sh >> /home/u665392393/backups/cron.log 2>&1
```

**Ou backup toutes les 6 heures (plus sûr) :**

```bash
0 */6 * * * /home/u665392393/domains/infpf.fr/dev/bin/backup-database.sh >> /home/u665392393/backups/cron.log 2>&1
```

### 3. Sauvegarder et quitter

- **Nano** : `Ctrl+X`, `Y`, `Enter`
- **Vim** : `:wq`

---

## 🧪 Tester le backup manuellement

```bash
cd /home/u665392393/domains/infpf.fr/dev
./bin/backup-database.sh
```

**Résultat attendu :**
```
[2025-11-05 15:30:00] =========================================
[2025-11-05 15:30:00] Début du backup de la base de données
[2025-11-05 15:30:00] =========================================
[2025-11-05 15:30:00] Base de données: infpf_db
[2025-11-05 15:30:00] Hôte: 127.0.0.1:3306
[2025-11-05 15:30:00] Utilisateur: root
[2025-11-05 15:30:01] Création du backup...
[SUCCESS] Backup créé avec succès : /home/u665392393/backups/database/infpf_db_2025-11-05_15-30-00.sql.gz (2.3M)
[2025-11-05 15:30:02] Nettoyage des backups de plus de 30 jours...
[2025-11-05 15:30:02] Aucun ancien backup à supprimer
[2025-11-05 15:30:02] =========================================
[2025-11-05 15:30:02] Backup terminé avec succès
[2025-11-05 15:30:02] Total backups: 1
[2025-11-05 15:30:02] Espace utilisé: 2.3M
[2025-11-05 15:30:02] =========================================
```

---

## 📁 Structure des backups

```
/home/u665392393/backups/
├── database/
│   ├── infpf_db_2025-11-05_03-00-00.sql.gz
│   ├── infpf_db_2025-11-06_03-00-00.sql.gz
│   ├── infpf_db_2025-11-07_03-00-00.sql.gz
│   └── ...
├── backup.log           # Logs détaillés du script
└── cron.log             # Logs du cron
```

---

## 🔄 Restaurer un backup

### 1. Décompresser le backup

```bash
gunzip /home/u665392393/backups/database/infpf_db_2025-11-05_03-00-00.sql.gz
```

### 2. Restaurer la base de données

```bash
mysql -u root -p infpf_db < /home/u665392393/backups/database/infpf_db_2025-11-05_03-00-00.sql
```

---

## 📊 Monitoring des backups

### Vérifier les derniers backups

```bash
ls -lh /home/u665392393/backups/database/ | tail -5
```

### Consulter les logs

```bash
tail -f /home/u665392393/backups/backup.log
```

### Vérifier que le cron fonctionne

```bash
grep CRON /var/log/syslog | tail -20
```

---

## 🛡️ Sécurité

### ✅ **Bonnes pratiques appliquées :**

1. **Compression gzip** → Économise de l'espace disque
2. **Rétention 30 jours** → Historique suffisant sans saturer
3. **Logs détaillés** → Traçabilité complète
4. **Single transaction** → Backup cohérent (pas de corruption)
5. **Routines & Triggers** → Sauvegarde complète de la structure

### ⚠️ **À améliorer (optionnel) :**

1. **Backup off-site** : Copier sur un serveur externe (AWS S3, Google Cloud Storage)
2. **Encryption** : Chiffrer les backups avant stockage
3. **Notification** : Email automatique en cas d'échec
4. **Test de restauration** : Automatiser la vérification d'intégrité

---

## 📝 Script amélioré pour notifications (BONUS)

Ajouter à la fin de `backup-database.sh` :

```bash
# Notification par email en cas d'échec
if [ $? -ne 0 ]; then
    echo "Backup failed at $(date)" | mail -s "BACKUP FAILED - INFPF" elyes@xeilos.fr
fi
```

**Nécessite** : `sudo apt install mailutils`

---

## 📞 Support

**En cas de problème :**
- Email : elyes@xeilos.fr
- Vérifier : `/home/u665392393/backups/backup.log`
- Tester manuellement : `./bin/backup-database.sh`

---

## ✅ Checklist finale

- [ ] Script créé et exécutable
- [ ] Test manuel effectué
- [ ] Cron configuré (crontab -e)
- [ ] Backup visible dans `/home/u665392393/backups/database/`
- [ ] Logs consultés et OK
- [ ] Test de restauration effectué (optionnel)

---

**Date de configuration** : 2025-11-05  
**Script** : `/bin/backup-database.sh`  
**Fréquence** : Quotidien à 3h du matin  
**Rétention** : 30 jours


