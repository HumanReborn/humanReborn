#!/bin/bash

cd ../../../../app/config && mv config.yml _config_tmp.yml && mv _config_two.yml config.yml && mv _config_tmp.yml _config_two.yml && mv parameters.yml _parameters_tmp.yml && mv _parameters_two.yml parameters.yml && mv _parameters_tmp.yml _parameters_two.yml && echo "DONE"

