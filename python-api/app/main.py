from fastapi import FastAPI
from app.core.config import settings
from app.routers import analysis_router

app = FastAPI(
    title=settings.PROJECT_NAME,
    version="0.1.0",
    description="Microservicio de análisis agroclimático con datos satelitales de la NASA",
)

app.include_router(analysis_router.router, prefix="/api/v1")


@app.get("/health", tags=["System"])
def health_check():
    return {"status": "ok", "service": settings.PROJECT_NAME}