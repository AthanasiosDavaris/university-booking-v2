#!/bin/bash

echo "====================================================="
echo " UCMS v2.0 - Concurrency Benchmarking Suite (Docker)"
echo " Thesis Candidate: Athanasios Davaris"
echo "====================================================="

# Define the strategies to test
STRATEGIES=("none" "optimistic" "pessimistic")

# Απευθείας επικοινωνία μέσω του εσωτερικού Docker Network
BASE_URL="http://ucms_nginx/api"
NETWORK="ucms_ucms_network"

for STRATEGY in "${STRATEGIES[@]}"
do
    echo ""
    echo "-----------------------------------------------------"
    echo " [ RUNNING STRATEGY: $STRATEGY ]"
    echo "-----------------------------------------------------"

    # Εκτέλεση k6 εντός του Docker Network
    docker run --rm -i \
     --network=$NETWORK \
     -e STRATEGY=$STRATEGY \
     -e BASE_URL=$BASE_URL \
     grafana/k6 run - < tests/k6/concurrency_test.js
    
    echo " -> Finished $STRATEGY. Cooling down for 5 seconds..."
    sleep 5
done

echo ""
echo "====================================================="
echo " Benchmarks Complete!"
echo "====================================================="