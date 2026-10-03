export interface HealthServices {
  database: boolean
  cache: boolean
}

export interface Health {
  isHealthy: boolean
  services: HealthServices
}
