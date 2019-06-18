#!/bin/bash

npm ci

# Keep the container running after building so that we can connect to rebuild assets.
tail -F /dev/null
