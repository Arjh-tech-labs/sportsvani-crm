import { NextResponse } from "next/server"
import { youtubeApi } from "@/lib/youtube-api"

// Create a live stream
export async function POST(request: Request) {
  try {
    const { title, description, privacy, matchId } = await request.json()

    if (!title) {
      return NextResponse.json({ success: false, message: "Title is required" }, { status: 400 })
    }

    // Create live stream via YouTube API
    const result = await youtubeApi.createLiveStream(title, description || "", privacy || "public")

    return NextResponse.json({
      success: true,
      message: "Live stream created successfully",
      streamId: result.streamId,
      streamUrl: result.streamUrl,
      streamKey: result.streamKey,
      watchUrl: result.watchUrl,
      matchId,
    })
  } catch (error) {
    console.error("Error creating live stream:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while creating live stream" },
      { status: 500 },
    )
  }
}

// Get match videos
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const matchId = searchParams.get("matchId")

    if (!matchId) {
      return NextResponse.json({ success: false, message: "Match ID is required" }, { status: 400 })
    }

    // Fetch videos via YouTube API
    const result = await youtubeApi.fetchMatchVideos(matchId)

    return NextResponse.json({
      success: true,
      videos: result.videos,
    })
  } catch (error) {
    console.error("Error fetching match videos:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while fetching match videos" },
      { status: 500 },
    )
  }
}

