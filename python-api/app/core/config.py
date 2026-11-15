from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    PROJECT_NAME: str = "NASA Agro Engine"
    NASA_POWER_URL: str = "https://power.larc.nasa.gov/api/temporal/daily/point"
    URL_MODIS_ORNL: str = "https://modis.ornl.gov/rst/api/v1/MOD13Q1/subset"
    URL_ELEVACION_OPEN_METEO: str = "https://api.open-meteo.com/v1/elevation"
    HTTP_TIMEOUT: float = 20.0

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8")


settings = Settings()