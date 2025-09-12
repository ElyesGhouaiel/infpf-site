echo "=== DATE ==="
date
echo
echo "=== CHARGE ==="
uptime
echo
echo "=== MEMOIRE ==="
free -m
echo
echo "=== DISQUE ==="
df -h
echo
echo "=== TES PROCESSUS LES PLUS GOURMANDS ==="
ps -u $USER --sort=-%mem | head -n 15
