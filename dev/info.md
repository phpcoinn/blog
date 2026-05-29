# Pull from container to host (one-time setup)
lxc file pull -r openclaw/root/.openclaw/workspace/blog/* /home/marko/web/phpcoin/blog/

# Work on files locally in /home/marko/web/phpcoin/blog/

# Push changes back to container
lxc file push -r /home/marko/web/phpcoin/blog/* openclaw/root/.openclaw/workspace/blog/

# Restart PHP server in container to see changes
lxc exec openclaw -- pkill -f "php -S"
lxc exec openclaw -- php -S 0.0.0.0:8080 -t /root/.openclaw/workspace/blog