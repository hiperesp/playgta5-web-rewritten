# Assets and identification

## Distribution

`mirror/playgta5.com/` is not distributed with this project. The directory may
contain copyrighted content. The author of this project is not responsible for
that file or claiming ownership of it: the available files were only used to
test the implementation, and the author does not actually know everything
they contain. The only functional knowledge is that the files present work
with the frontend.

Each user must find the assets independently and confirm that they have
permission to obtain and use them. Do not include this directory in releases,
Docker images, or public repositories.

## Provided identifiers

The hashes provided for the asset directory/collection are:

```text
SHA-256: 669B4FAFC1E7B232B19001C853ABA2B787A4B2F93B28B1E3F0ECAD1D13B466D0
MD5:     B877DA284DF154F8BDE9CE71F92B1054
```

These values are comparable only when they are produced from exactly the same
bytes and using the same method to represent a directory. Directories do not
have a universal hash: different tools may sort files differently or include
names, permissions, and timestamps.

## Useful additional hashes

The most common additional identifier would be **SHA-1**, but it should be
calculated over a distributable file, such as a ZIP or 7z archive, rather than
implicitly over a directory. SHA-1 is useful for finding matches in older
listings, but it is cryptographically obsolete for security verification.

For modern identification, the best options are:

1. SHA-256 of an archive or an official image/manifest;
2. SHA-1 of the same file for compatibility with older sources;
3. total size in bytes;
4. file count;
5. the version listed in `data/manifest.json` or `data-manifest.json`;
6. the build identifier, such as the directory inside `b/`.

MD5 may remain published as a historical identifier, but it should not be used
alone to guarantee integrity. BLAKE3 is a modern and fast option, but it is
less common for searching for files online.

It is not recommended to calculate a hash by concatenating files in an
arbitrary order. If a directory must be identified without archiving it,
publish a deterministic manifest containing each relative path, size, and
individual SHA-256, sorted by path, and then publish the SHA-256 of that
manifest.
