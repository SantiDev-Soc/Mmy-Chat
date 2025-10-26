#!/bin/bash

# 🟢 Lanza Laravel HTTP en segundo plano
php artisan serve --host=0.0.0.0 --port=8030 &

# 🔊 Lanza Reverb en primer plano
php artisan reverb:start
