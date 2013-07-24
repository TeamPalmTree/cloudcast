##############
## SWEEPERS ##
##############

# get sweepers playlist url
sweepers_url = string.replace(pattern = '<genre>', fun(s) -> !sweeper_genre, files_genre_uri)
# pre-load sweepers playlist, set reload strategy
sweepers = audio_to_stereo(
    playlist(
        mime_type = 'audio/mpegurl',
        reload = 1,
        reload_mode = 'rounds',
        sweepers_url
    )
)

#############
## JINGLES ##
#############

# get jingles playlist url
jingles_url = string.replace(pattern = '<genre>', fun(s) -> !jingle_genre, files_genre_uri)
# pre-load jingles playlist, set reload strategy
jingles = audio_to_stereo(
    playlist(
        mime_type = 'audio/mpegurl',
        reload = 1,
        reload_mode = 'rounds',
        jingles_url
    )
)