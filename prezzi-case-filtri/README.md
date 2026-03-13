# Prezzi Case Prefabbricate - Filtro Modelli

Plugin WordPress per cercare modelli di case prefabbricate tramite 5 menu a tendina (categorie multilivello):

- Tipologia abitativa
- Numero di piani
- Numero di camere da letto
- Numero di bagni
- Range di prezzo

## Installazione

1. Comprimi la cartella `prezzi-case-filtri` in un file ZIP.
2. In WordPress vai su **Plugin > Aggiungi nuovo > Carica plugin**.
3. Carica lo ZIP e attiva il plugin.

## Utilizzo

Inserisci lo shortcode dove vuoi mostrare il filtro:

```txt
[prefab_house_filter]
```

## Struttura categorie consigliata

Per usare i filtri di default, crea 5 categorie radice con slug:

- `tipologia-abitativa`
- `numero-di-piani`
- `numero-di-camere-da-letto`
- `numero-di-bagni`
- `range-di-prezzo`

Sotto ogni radice puoi creare categorie figlie e sotto-categorie: verranno mostrate nel menu a tendina con indentazione.

## Personalizzazione (opzionale)

Puoi cambiare slug/etichette usando il filtro `pcf_filter_groups` nel tema child o in un plugin custom.
