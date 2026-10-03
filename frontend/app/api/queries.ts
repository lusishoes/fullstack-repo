/** Сносит все записи кэша Colada: своего `clear()` у пакета нет. */
export function clearQueryCache(): void {
  const queryCache = useQueryCache()

  queryCache.cancelQueries()
  queryCache.getEntries().forEach((entry) => {
    queryCache.remove(entry)
  })
}
