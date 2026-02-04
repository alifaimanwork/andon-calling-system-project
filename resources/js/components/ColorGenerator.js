export class ColorGenerator {
    coolColor = [
        [48, 79, 254],
        [0, 145, 234],
        [0, 191, 165],
    ];
    hotColor = [
        [213, 0, 0],
        [233, 30, 99],
        [221, 44, 0],
    ];
    baseColor = [
        [213, 0, 0],
        [255, 109, 0],
        [255, 214, 0],
        [100, 221, 23],
        [0, 191, 165],
        [41, 98, 255],
        [170, 0, 255],
    ];
    stepping = [0, 0.5, 0.75, -0.5, 0.25];
    generateColor(index, alpha = 1) {
        if (this.baseColor.length <= 0)
            return `rgba(0,0,0,${alpha})`;

        let idx = index % this.baseColor.length;
        let step = Math.floor(index / this.baseColor.length);

        let delta
        let mul = this.stepping[step];

        if (mul > 0)
            delta = [
                255 - this.baseColor[idx][0],
                255 - this.baseColor[idx][1],
                255 - this.baseColor[idx][2],
            ];
        else
            delta = [
                this.baseColor[idx][0],
                this.baseColor[idx][1],
                this.baseColor[idx][2],
            ];

        let generated = [
            Math.round(this.baseColor[idx][0] + delta[0] * mul),
            Math.round(this.baseColor[idx][1] + delta[1] * mul),
            Math.round(this.baseColor[idx][2] + delta[2] * mul),
        ];

        return `rgba(${generated[0]},${generated[1]},${generated[2]},${alpha})`;
    }

}