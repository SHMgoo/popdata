import json

TOPO_PATH = "topo_11.json"
POP_PATH = "fips_pop.json"
OUTPUT_PATH = "world.geojson"

with open(TOPO_PATH) as f:
    topo = json.load(f)

with open(POP_PATH) as f:
    pop = json.load(f)

scale = topo["transform"]["scale"]
translate = topo["transform"]["translate"]
arcs = topo["arcs"]

def decode_arc(arc):
    x = 0
    y = 0
    coords = []
    for dx, dy in arc:
        x += dx
        y += dy
        coords.append([x * scale[0] + translate[0], y * scale[1] + translate[1]])
    return coords

decoded = [decode_arc(arc) for arc in arcs]

def extract_arc(index):
    if index >= 0:
        return decoded[index]
    return list(reversed(decoded[~index]))

def stitch(indices):
    coords = []
    for idx in indices:
        arc = extract_arc(idx)
        if coords:
            coords.extend(arc[1:])
        else:
            coords.extend(arc)
    if coords and coords[0] != coords[-1]:
        coords.append(coords[0])
    return coords

features = []
for geom in topo["objects"]["global_map"]["geometries"]:
    gid = geom.get("id")
    props = {"id": gid}

    if gid in pop:
        props["name"] = pop[gid].get("st_name")
        raw_pop = pop[gid].get("pop")
        try:
            props["population"] = int(raw_pop.replace(",", "")) if isinstance(raw_pop, str) and raw_pop.strip() else None
        except Exception:
            props["population"] = None

    if geom["type"] == "Polygon":
        geometry = {
            "type": "Polygon",
            "coordinates": [stitch(ring) for ring in geom["arcs"]],
        }
    elif geom["type"] == "MultiPolygon":
        geometry = {
            "type": "MultiPolygon",
            "coordinates": [[stitch(ring) for ring in polygon] for polygon in geom["arcs"]],
        }
    else:
        raise ValueError(f"Unhandled geometry type: {geom['type']}")

    features.append({
        "type": "Feature",
        "id": gid,
        "properties": props,
        "geometry": geometry,
    })

geojson = {
    "type": "FeatureCollection",
    "features": features,
}

with open(OUTPUT_PATH, "w") as f:
    json.dump(geojson, f)

print(f"Wrote {OUTPUT_PATH} with {len(features)} features.")
