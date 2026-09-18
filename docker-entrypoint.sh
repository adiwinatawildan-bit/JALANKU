#!/bin/bash
set -e

# Support Render custom PORT
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/*.conf

# Set writable directories and offline flags for AI / YOLO
export YOLO_CONFIG_DIR=/tmp
export TORCH_HOME=/tmp
export YOLO_OFFLINE=True
export ULTRALYTICS_OFFLINE=True
export YOLO_AUTOINSTALL=0
export ULTRALYTICS_AUTOINSTALL=0
export CUDA_VISIBLE_DEVICES=""

# Laravel Setup
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache on port $PORT..."
exec apache2-foreground
