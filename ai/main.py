from fastapi import FastAPI

app = FastAPI(root_path="/ai")

@app.get("/")
def health_check():
    return {"status": "AI Engine Operational", "service": "ucms-ai"}