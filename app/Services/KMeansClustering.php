<?php

namespace App\Services;

class KMeansClustering
{
    /**
     * Perform K-Means clustering on the given data.
     *
     * @param array $data Array of ['id' => ..., 'features' => [x, y]]
     * @param int $k Number of clusters
     * @param int $maxIterations Maximum number of iterations
     * @return array Array of ['id' => ..., 'cluster' => ...]
     */
    public function perform(array $data, int $k, int $maxIterations = 100): array
    {
        if (count($data) < $k) {
            // Not enough data points for K clusters, assign all to cluster 0
            return array_map(function ($item) {
                return ['id' => $item['id'], 'cluster' => 0];
            }, $data);
        }

        // 1. Normalize Data (Min-Max Scaling)
        $normalizedData = $this->normalize($data);

        // 2. Initialize Centroids (Randomly pick K points)
        $centroids = $this->initializeCentroids($normalizedData, $k);

        $assignments = [];
        for ($i = 0; $i < $maxIterations; $i++) {
            // 3. Assign points to nearest centroid
            $newAssignments = $this->assignClusters($normalizedData, $centroids);

            // Check for convergence (if assignments haven't changed)
            if ($this->hasConverged($assignments, $newAssignments)) {
                $assignments = $newAssignments;
                break;
            }

            $assignments = $newAssignments;

            // 4. Update Centroids
            $centroids = $this->updateCentroids($normalizedData, $assignments, $k);
        }

        // Return results with original IDs
        $results = [];
        foreach ($assignments as $index => $clusterIndex) {
            $results[] = [
                'id' => $data[$index]['id'],
                'cluster' => $clusterIndex
            ];
        }

        return $results;
    }

    private function normalize(array $data): array
    {
        $features = array_column($data, 'features');
        if (empty($features)) return [];

        $numFeatures = count($features[0]);
        $min = array_fill(0, $numFeatures, PHP_FLOAT_MAX);
        $max = array_fill(0, $numFeatures, PHP_FLOAT_MIN);

        // Find min and max for each feature
        foreach ($features as $row) {
            foreach ($row as $i => $val) {
                if ($val < $min[$i]) $min[$i] = $val;
                if ($val > $max[$i]) $max[$i] = $val;
            }
        }

        // Normalize
        $normalized = [];
        foreach ($data as $item) {
            $normFeatures = [];
            foreach ($item['features'] as $i => $val) {
                $range = $max[$i] - $min[$i];
                $normFeatures[$i] = $range == 0 ? 0 : ($val - $min[$i]) / $range;
            }
            $normalized[] = ['id' => $item['id'], 'features' => $normFeatures];
        }

        return $normalized;
    }

    private function initializeCentroids(array $data, int $k): array
    {
        $centroids = [];
        $indices = array_rand($data, $k);
        if (!is_array($indices)) $indices = [$indices];

        foreach ($indices as $index) {
            $centroids[] = $data[$index]['features'];
        }
        return $centroids;
    }

    private function assignClusters(array $data, array $centroids): array
    {
        $assignments = [];
        foreach ($data as $item) {
            $minDist = PHP_FLOAT_MAX;
            $cluster = 0;

            foreach ($centroids as $k => $centroid) {
                $dist = $this->euclideanDistance($item['features'], $centroid);
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $cluster = $k;
                }
            }
            $assignments[] = $cluster;
        }
        return $assignments;
    }

    private function updateCentroids(array $data, array $assignments, int $k): array
    {
        $centroids = array_fill(0, $k, []);
        $counts = array_fill(0, $k, 0);
        $numFeatures = count($data[0]['features']);

        // Initialize sums
        for ($j = 0; $j < $k; $j++) {
            $centroids[$j] = array_fill(0, $numFeatures, 0);
        }

        // Sum features for each cluster
        foreach ($data as $i => $item) {
            $cluster = $assignments[$i];
            $counts[$cluster]++;
            foreach ($item['features'] as $f => $val) {
                $centroids[$cluster][$f] += $val;
            }
        }

        // Calculate average
        for ($j = 0; $j < $k; $j++) {
            if ($counts[$j] > 0) {
                foreach ($centroids[$j] as $f => $val) {
                    $centroids[$j][$f] /= $counts[$j];
                }
            } else {
                // Handle empty cluster: re-initialize randomly or keep previous (simplified: keep 0s or random)
                // For robustness, let's just keep it as is (0) effectively removing it or placing it at origin
                // Better approach: Re-initialize to a random point to try to find a new cluster
                 $centroids[$j] = $data[array_rand($data)]['features'];
            }
        }

        return $centroids;
    }

    private function euclideanDistance(array $p1, array $p2): float
    {
        $sum = 0;
        foreach ($p1 as $i => $val) {
            $sum += pow($val - $p2[$i], 2);
        }
        return sqrt($sum);
    }

    private function hasConverged(array $old, array $new): bool
    {
        if (empty($old)) return false;
        return $old === $new;
    }
}
