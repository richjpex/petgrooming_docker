#!/bin/bash
set -e

# --- 1. Stop and remove containers, networks, and images ---
echo "Stopping and removing containers..."
docker-compose down

# --- 2. Remove the MySQL data volume ---

# WARNING: This step deletes all data inside the 'petgrooming_erp_mysqldata' volume.
echo ""
read -p "Do you want to delete the volume 'petgrooming_erp_mysqldata'? This will remove all data. [y/N]: " confirm
if [[ "$confirm" =~ ^[Yy]$ ]]; then
	echo "Removing volume: petgrooming_erp_mysqldata"
	docker volume rm petgrooming_erp_mysqldata || true
else
	echo "Skipping volume removal."
fi


# --- 3. Build and start services in detached mode ---
echo ""
echo "Building and starting services..."
docker-compose up -d --build

echo ""
echo "Docker services are up and running!"