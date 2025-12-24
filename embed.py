from sentence_transformers import SentenceTransformer
import sys
import json
import numpy as np

with open(sys.argv[1], 'r', encoding='utf-8') as f:
    text1 = f.read()

with open(sys.argv[2], 'r', encoding='utf-8') as f:
    text2 = f.read()

model = SentenceTransformer('sentence-transformers/all-MiniLM-L6-v2')

embedding1 = model.encode(text1)
embedding2 = model.encode(text2)

cosine_similarity = float(np.dot(embedding1, embedding2) / (np.linalg.norm(embedding1) * np.linalg.norm(embedding2)))

print(json.dumps({"score": cosine_similarity}))
