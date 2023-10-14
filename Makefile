# Makefile pour Symfony

# Variables
CONSOLE= symfony console

# Commandes par défaut
default: help

# Aide
help:
	@echo "Utilisez 'make [commande]' où '[commande]' est l'une des commandes suivantes:"
	@echo "  entity       Pour créer une nouvelle entité"
	@echo "  crud         Pour générer un CRUD pour une entité"
	@echo "  form         Pour créer un formulaire pour une entité"
	@echo "  migration    Pour créer une nouvelle migration"
	@echo "  migrate      Pour exécuter les migrations"
	@echo "  user         Pour créer une entité utilisateur"
	@echo "  validator    Pour créer une nouvelle contrainte de validation"
	@echo "  command      Pour créer une nouvelle commande console"

# Créer une entité
entity:
	$(CONSOLE) make:entity

# Générer un CRUD
crud:
	$(CONSOLE) make:crud

# Créer un formulaire
form:
	$(CONSOLE) make:form

# Créer une migration
migration:
	$(CONSOLE) make:migration

# Exécuter les migrations
migrate:
	$(CONSOLE) doctrine:migrations:migrate

# Créer une entité utilisateur
user:
	$(CONSOLE) make:user

# Créer une contrainte de validation
validator:
	$(CONSOLE) make:validator

# Créer une commande console
command:
	$(CONSOLE) make:command
# Créer une commande console
controller:
	$(CONSOLE) make:controller
#créer fixture
fixtures:
	$(CONSOLE) doctrine:fixtures:load

.PHONY: help entity crud form migration migrate user validator command

# Build application
build:
	npm run build
watch:
	npm run watch

start:
	docker compose up -d
	symfony serve

stop:
	symfony serve:stop
	docker compose stop
