<h1>Foto <?=$index + 1?> / <?=count($images) ?></h1>

<div style="display: flex">
    <div>
        <img src="/image/<?=$index?>" alt="Foto kleding" id="image" />
    </div>
    <form style="margin-left: 2rem" id="form" action="/save" method="post">
        <input type="hidden" name="index" value="<?=$index?>" />
        <p>
            <span>Maat: <br /></span>
            <label for="size_50"><input type="radio" name="size" id="size_50" value="50" />50</label>
            <label for="size_56"><input type="radio" name="size" id="size_56" value="56" />56</label>
            <label for="size_62"><input type="radio" name="size" id="size_62" value="62" />62</label>
            <label for="size_6268"><input type="radio" name="size" id="size_6268" value="62/68" />62/68</label>
            <label for="size_68"><input type="radio" name="size" id="size_68" value="68" />68</label>
            <label for="size_74"><input type="radio" name="size" id="size_74" value="74" />74</label>
            <label for="size_80"><input type="radio" name="size" id="size_80" value="80" />80</label>
            <label for="size_86"><input type="radio" name="size" id="size_86" value="86" />86</label>
            <label for="size_92"><input type="radio" name="size" id="size_92" value="92" />92</label>
            <label for="size_98"><input type="radio" name="size" id="size_98" value="98" />98</label>
            <label for="size_104"><input type="radio" name="size" id="size_104" value="104" />104</label>
        </p>
        <p>
            <label for="brand">Merk: <br />
                <input type="text" name="brand" id="brand" value="" /></label>
        </p>
        <p>
            <label for="state">Staat: <br />
                <input type="text" name="state" id="state" value="" /></label>
        </p>
        <p>
            <label for="price">Startprijs: <br />
            <input type="number" name="price" id="price" value="" /></label>
        </p>
        <p>
            <label for="halfbid">
                <input type="checkbox" id="halfbid" name="halfbid" value="1" />
                Opbieden per halve euro toestaan
            </label>
        </p>
        <p>
            <label for="pickup">
                <input type="checkbox" id="pickup" name="pickup" value="1" />
                Enkel op te halen bij verkoper of regioverantwoordelijke
            </label>
        </p>
        <p>
            <button type="submit">Opslaan</button>
        </p>
    </form>
</div>

<script>
    const form = document.getElementById('form');
    const image = document.getElementById('image');

    form.addEventListener('change', function () {
        const data  = new FormData(form);
        const query = new URLSearchParams();

        for (const [key, value] of data) {
            if (value) {
                query.append(key, value);
            }
        }

        const src = new URL(image.src);
        src.search = query.toString();

        image.src = src.toString();
    });
</script>
